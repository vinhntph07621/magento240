<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 9/8/17
 * Time: 12:12 AM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::locations';

    protected $resourceKey = 'Omnyfy_Vendor::locations';

    protected $adminTitle = 'Location';

    protected $profileResource;

    protected $vendorResource;

    protected $backendJsHelper;

    protected $locationFactory;

    protected $vendorRepository;

    protected $vendorTypeRepository;

    public function __construct(
        \Magento\Backend\Helper\Js $backendJsHelper,
        \Omnyfy\Vendor\Model\Resource\Profile $profileResource,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\LocationFactory $locationFactory,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository
    )
    {
        $this->backendJsHelper = $backendJsHelper;
        $this->profileResource = $profileResource;
        $this->vendorResource = $vendorResource;
        $this->locationFactory = $locationFactory;
        $this->vendorRepository = $vendorRepository;
        $this->vendorTypeRepository = $vendorTypeRepository;

        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (empty($data)) {
            $this->_redirect('omnyfy_vendor/*/');
            return;
        }

        try {
            $model = $this->locationFactory->create();

            $inputFilter = new \Zend_Filter_Input(
                [], [], $data
            );
            $data = $inputFilter->getUnescaped();
            $data = $data['location'];
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
                if ($id != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The wrong location is specified.'));
                }
            }
            if (!array_key_exists('vendor_id', $data)){
                throw new \Magento\Framework\Exception\LocalizedException(__('Vendor not specified.'));
            }
            $data['vendor_id'] = intval($data['vendor_id']);
            if (empty($data['vendor_id'])){
                throw new \Magento\Framework\Exception\LocalizedException(__('Vendor not specified.'));
            }
            $vendor = $this->vendorRepository->getById($data['vendor_id']);
            if (0==$vendor->getId()) {
                throw new LocalizedException(__('Vendor does not exist any more'));
            }
            $vendorType = $this->vendorTypeRepository->getById($vendor->getTypeId());
            if (0== $vendorType->getId()) {
                throw new LocalizedException(__('Vendor Type for this location is missing'));
            }

            $model->addData($data);

            if (isset($data['longitude']) && !empty($data['longitude'])) {
                $radLon = $data['longitude'] * M_PI / 180;
                $model->setData('lon', $data['longitude']);
                $model->setData('rad_lon', $radLon);
            }
            if (isset($data['latitude']) && !empty($data['latitude'])) {
                $radLat = $data['latitude'] * M_PI / 180;
                $model->setData('lat', $data['latitude']);
                $model->setData('rad_lat', $radLat);
                $model->setData('cos_lat', cos($radLat));
                $model->setData('sin_lat', sin($radLat));
            }
            $model->setData('vendor_type_id', $vendorType->getId());
            $model->setData('attribute_set_id', $vendorType->getLocationAttributeSetId());

            $this->_session->setPageData($model->getData());
            $model->save();
            if ($model->getId()) {
                // get all profile ids by vendor Id and websiteIds
                $this->updateProfileRelation(
                    $model->getId(),
                    $this->profileResource->getProfileIdsByLocationId($model->getId()),
                    $this->getProfileIds($model->getVendorId(), $data['website_ids'])
                );

                $vendorInfo = $this->_session->getVendorInfo();

                //update vendor session
                if (!empty($vendorInfo)) {
                    $locationIds = [];
                    foreach($vendorInfo['location_ids'] as $locationId) {
                        if ($locationId > 0) {
                            $locationIds[] = $locationId;
                        }
                    }
                    $locationIds[] = $model->getId();
                    $vendorInfo['location_ids'] = array_unique($locationIds);
                    $this->_session->setVendorInfo($vendorInfo);
                }
            }
            $this->messageManager->addSuccessMessage(__('You saved the location.'));

            $this->_session->setPageData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('omnyfy_vendor/*/edit', ['id' => $model->getId()]);
                return;
            }
            $this->_redirect('omnyfy_vendor/*/');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int) $this->getRequest()->getParam('id');
            if (!empty($id)) {
                $this->_redirect('omnyfy_vendor/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('omnyfy_vendor/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                'Something went wrong while saving the location data. Please review the error log.'
            );
            $this->_logger->critical($e);
            $this->_session->setPageData($data);
            $this->_redirect('omnyfy_vendor/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
    }

    protected function getProfileIds($vendorId, $websiteIds)
    {
        $profileIds = $this->profileResource->getProfileIdsByVendorId($vendorId);
        $result = [];
        foreach($websiteIds as $websiteId) {
            if (array_key_exists($websiteId, $profileIds)) {
                $result[] = $profileIds[$websiteId];
            }
        }
        return $result;
    }

    protected function updateProfileRelation($locationId, $profileIds, $data)
    {
        $toRemove = [];
        $toRemove['location_id'] = $locationId;
        $toRemoveProfileIds = array_diff($profileIds, $data);
        if (!empty($toRemoveProfileIds)) {
            $toRemove['profile_id'] = array_diff($profileIds, $data);
            $this->profileResource->removeLocationRelation($toRemove);
        }

        $toAdd = [];
        $toAddProfileIds = array_diff($data, $profileIds);
        foreach($toAddProfileIds as $profileId) {
            $toAdd[] = [
                'profile_id' => $profileId,
                'location_id' => $locationId,
            ];
        }
        $this->profileResource->saveLocationRelation($toAdd);
    }
}