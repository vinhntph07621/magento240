<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 18/7/17
 * Time: 10:41 AM
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor;

use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendors';
    protected $resourceKey = 'Omnyfy_Vendor::vendors';

    protected $adminTitle = 'Vendors';

    protected $imageAdapterFactory;

    protected $vendorFactory;

    protected $profileResource;

    protected $uploaderFactory;

    protected $mediaDirectory;

    protected $vendorTypeRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Image\AdapterFactory $imageAdapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Model\Resource\Profile $profileResource,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository
    )
    {
        $this->vendorTypeRepository = $vendorTypeRepository;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);

        $this->imageAdapterFactory = $imageAdapterFactory;

        $this->vendorFactory = $vendorFactory;
        $this->profileResource = $profileResource;
        $this->uploaderFactory = $uploaderFactory;

        $this->mediaDirectory = $filesystem->getDirectoryRead(
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
    }

    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();
            $id = (int)$this->getRequest()->getParam('id');

            try {
                if (!isset($data['website_ids']) || empty($data['website_ids'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please select a marketplace.'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $vendor_id = isset($data['id']) ? intval($data['id']) : 0;

                $this->_session->setPageData(false);
                if (!empty($vendor_id)) {
                    $this->_redirect('omnyfy_vendor/*/edit', ['id' => $vendor_id, '_current' => true]);
                } else {
                    $this->_redirect('omnyfy_vendor/*/new');
                }
                return;
            }

            try {
                $model = $this->vendorFactory->create();

                $inputFilter = new \Zend_Filter_Input([], [], $data);

                $data = $inputFilter->getUnescaped();

                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong vendor is specified.'));
                    }
                }

                //Trigger data validation event
                $this->_eventManager->dispatch('omnyfy_vendor_form_validation', ['form_data' => $data]);

                $model->addData($data);

                $this->_session->setPageData($model->getData());

                //set vendor attribute_set_id by type_id
                $typeId = isset($data['type_id']) ? intval($data['type_id']) : 0;
                $vendorType = $this->vendorTypeRepository->getById($typeId);
                $model->setData('attribute_set_id', $vendorType->getVendorAttributeSetId());

                $model->save();
                if ($model->getId()) {
                    //profile updates moved to observer
                    $this->_eventManager->dispatch('omnyfy_vendor_update_website_ids',
                        [
                            'website_ids' => $data['website_ids'],
                            'vendor_id' => $model->getId()
                        ]
                    );
                }

                //save location attribute_set_id based on vendor type_id
                //logic moved to vendor save after observer

                //Trigger after save event
                $this->_eventManager->dispatch('omnyfy_vendor_form_after_save',
                    [
                        'vendor' => $model,
                        'form_data'=> $data,
                        'is_new' => empty($id)
                    ]
                );

                $this->messageManager->addSuccessMessage('You saved the vendor.');
                $this->_session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('omnyfy_vendor/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('omnyfy_vendor/*/');
                return;
            } catch (NoSuchEntityException $e1) {
                $this->messageManager->addErrorMessage('Invalid vendor type selected');
                if (!empty($id)) {
                    $this->_redirect('omnyfy_vendor/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('omnyfy_vendor/*/new');
                }
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $el) {
                $this->messageManager->addErrorMessage($el->getMessage());

                if (!empty($id)) {
                    $this->_redirect('omnyfy_vendor/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('omnyfy_vendor/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    'Something went wrong while saving the vendor data. Please review the error log.'
                );
                $this->_logger->critical($e);
                $this->_session->setPageData($data);
                if (!empty($id)) {
                    $this->_redirect('omnyfy_vendor/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('omnyfy_vendor/*/new');
                }
                return;
            }
        }
        $this->_redirect('omnyfy_vendor/*/');
    }
}
