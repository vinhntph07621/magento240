<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-30
 * Time: 15:25
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Omnyfy\Vendor\Helper\Backend as VendorHelper;
use Omnyfy\Vendor\Api\Data\VendorAttributeInterface;
use Omnyfy\Vendor\Model\Vendor\AttributeSet\BuildFactory;
use Omnyfy\Vendor\Model\Resource\Vendor\Eav\AttributeFactory;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\App\ObjectManager;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;
use Magento\Framework\Indexer\IndexerInterfaceFactory;

class Save extends \Omnyfy\Vendor\Controller\Adminhtml\Vendor\Attribute
{
    /**
     * @var BuildFactory
     */
    protected $buildFactory;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var VendorHelper
     */
    protected $vendorHelper;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var FormData
     */
    private $formDataSerializer;

    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    /**
     * @var IndexerCollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * @var IndexerInterfaceFactory
     */
    private $indexerInterfaceFactory;

    /**
     * @param Context $context
     * @param FrontendInterface $attributeLabelCache
     * @param Registry $coreRegistry
     * @param BuildFactory $buildFactory
     * @param PageFactory $resultPageFactory
     * @param AttributeFactory $attributeFactory
     * @param ValidatorFactory $validatorFactory
     * @param CollectionFactory $groupCollectionFactory
     * @param FilterManager $filterManager
     * @param VendorHelper $vendorHelper
     * @param LayoutFactory $layoutFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        FrontendInterface $attributeLabelCache,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        BuildFactory $buildFactory,
        AttributeFactory $attributeFactory,
        ValidatorFactory $validatorFactory,
        CollectionFactory $groupCollectionFactory,
        FilterManager $filterManager,
        VendorHelper $vendorHelper,
        LayoutFactory $layoutFactory,
        IndexerFactory $indexerFactory,
        IndexerCollectionFactory $indexerCollectionFactory,
        IndexerInterfaceFactory $indexerInterfaceFactory,
        FormData $formDataSerializer = null
    ) {
        parent::__construct($context, $attributeLabelCache, $coreRegistry, $resultPageFactory);
        $this->buildFactory = $buildFactory;
        $this->filterManager = $filterManager;
        $this->vendorHelper = $vendorHelper;
        $this->attributeFactory = $attributeFactory;
        $this->validatorFactory = $validatorFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->layoutFactory = $layoutFactory;
        $this->indexerFactory = $indexerFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->indexerInterfaceFactory = $indexerInterfaceFactory;
        $this->formDataSerializer = $formDataSerializer ?? ObjectManager::getInstance()->get(FormData::class);
    }

    /**
     * @return Redirect
     * @throws NotFoundException
     * @throws \Zend_Validate_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Page not found'));
        }

        try {
            $optionData = $this->formDataSerializer->unserialize(
                $this->getRequest()->getParam('serialized_options', '[]')
            );
        } catch (\InvalidArgumentException $e) {
            $message = __("The attribute couldn't be saved due to an error. Verify your information and try again. "
                . "If the error persists, please try again later.");
            $this->messageManager->addErrorMessage($message);

            return $this->returnResult('vendor_attribute/*/edit', ['_current' => true], ['error' => true]);
        }

        $data = $this->getRequest()->getPostValue();
        $data = array_replace_recursive(
            $data,
            $optionData
        );

        if ($data) {
            $setId = $this->getRequest()->getParam('set');

            $attributeSet = null;
            if (!empty($data['new_attribute_set_name'])) {
                $name = $this->filterManager->stripTags($data['new_attribute_set_name']);
                $name = trim($name);

                try {
                    /** @var $attributeSet Set */
                    $attributeSet = $this->buildFactory->create()
                        ->setEntityTypeId($this->_entityTypeId)
                        ->setSkeletonId($setId)
                        ->setName($name)
                        ->getAttributeSet();
                } catch (AlreadyExistsException $alreadyExists) {
                    $this->messageManager->addErrorMessage(__('An attribute set named \'%1\' already exists.', $name));
                    $this->_session->setAttributeData($data);
                    return $this->returnResult('omnyfy_vendor/*/edit', ['_current' => true], ['error' => true]);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while saving the attribute.')
                    );
                }
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');

            /** @var $model VendorAttributeInterface */
            $model = $this->attributeFactory->create();
            if ($attributeId) {
                $model->load($attributeId);
            }
            $attributeCode = $model && $model->getId()
                ? $model->getAttributeCode()
                : $this->getRequest()->getParam('attribute_code');
            $attributeCode = $attributeCode ?: $this->generateCode($this->getRequest()->getParam('frontend_label')[0]);
            if (strlen($attributeCode) > 0) {
                $validatorAttrCode = new \Zend_Validate_Regex(
                    ['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']
                );
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );
                    return $this->returnResult(
                        'omnyfy_vendor/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true],
                        ['error' => true]
                    );
                }
            }
            $data['attribute_code'] = $attributeCode;

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                //We allow 'image' type for vendor attribute
                if ('image' !== $data['frontend_input']) {
                    /** @var $inputType Validator */
                    $inputType = $this->validatorFactory->create();
                    if (!$inputType->isValid($data['frontend_input'])) {
                        foreach ($inputType->getMessages() as $message) {
                            $this->messageManager->addErrorMessage($message);
                        }
                        return $this->returnResult(
                            'omnyfy_vendor/*/edit',
                            ['attribute_id' => $attributeId, '_current' => true],
                            ['error' => true]
                        );
                    }
                }
            }

            if ($attributeId) {
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));
                    return $this->returnResult('omnyfy_vendor/*/', [], ['error' => true]);
                }
                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $this->messageManager->addErrorMessage(__('We can\'t update the attribute.'));
                    $this->_session->setAttributeData($data);
                    return $this->returnResult('omnyfy_vendor/*/', [], ['error' => true]);
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            } else {
                /**
                 * @todo add to helper and specify all relations for properties
                 */
                $data['source_model'] = $this->vendorHelper->getAttributeSourceModelByInputType(
                    $data['frontend_input']
                );
                $data['backend_model'] = $this->vendorHelper->getAttributeBackendModelByInputType(
                    $data['frontend_input']
                );
            }

            $data += ['is_filterable' => 0, 'is_filterable_in_search' => 0, 'apply_to' => []];

            if ($model->getIsUserDefined() === null || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
                if ('image' == $data['frontend_input']) {
                    $data['backend_type'] = 'varchar';
                }
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (!$model->getIsUserDefined() && $model->getId()) {
                // Unset attribute field for system attributes
                unset($data['apply_to']);
            }

            $model->addData($data);

            if (!$attributeId) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }

            $groupCode = $this->getRequest()->getParam('group');
            if ($setId && $groupCode) {
                // For creating product attribute on product page we need specify attribute set and group
                $attributeSetId = $attributeSet ? $attributeSet->getId() : $setId;
                $groupCollection = $this->groupCollectionFactory->create()
                    ->setAttributeSetFilter($attributeSetId)
                    ->addFieldToFilter('attribute_group_code', $groupCode)
                    ->setPageSize(1)
                    ->load();

                $group = $groupCollection->getFirstItem();
                if (!$group->getId()) {
                    $group->setAttributeGroupCode($groupCode);
                    $group->setSortOrder($this->getRequest()->getParam('groupSortOrder'));
                    $group->setAttributeGroupName($this->getRequest()->getParam('groupName'));
                    $group->setAttributeSetId($attributeSetId);
                    $group->save();
                }

                $model->setAttributeSetId($attributeSetId);
                $model->setAttributeGroupId($group->getId());
            }

            try {
                $model->save();

                $this->indexerInterfaceFactory->create()->load('omnyfy_vendor_vendor_flat')->reindexAll();;

                $this->messageManager->addSuccessMessage(__('You saved the vendor attribute.'));

                $this->_attributeLabelCache->clean();
                $this->_session->setAttributeData(false);
                if ($this->getRequest()->getParam('popup')) {
                    $requestParams = [
                        'attributeId' => $this->getRequest()->getParam('vendor'),
                        'attribute' => $model->getId(),
                        '_current' => true,
                        'vendor_tab' => $this->getRequest()->getParam('vendor_tab'),
                    ];
                    if ($attributeSet !== null) {
                        $requestParams['new_attribute_set_id'] = $attributeSet->getId();
                    }
                    return $this->returnResult('omnyfy_vendor/vendor/addAttribute', $requestParams, ['error' => false]);
                } elseif ($this->getRequest()->getParam('back', false)) {
                    return $this->returnResult(
                        'omnyfy_vendor/*/edit',
                        ['attribute_id' => $model->getId(), '_current' => true],
                        ['error' => false]
                    );
                }
                return $this->returnResult('omnyfy_vendor/*/', [], ['error' => false]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setAttributeData($data);
                return $this->returnResult(
                    'omnyfy_vendor/*/edit',
                    ['attribute_id' => $attributeId, '_current' => true],
                    ['error' => true]
                );
            }
        }

        return $this->returnResult('omnyfy_vendor/*/', [], ['error' => true]);
    }

    /**
     * @param string $path
     * @param array $params
     * @param array $response
     * @return Json|Redirect
     */
    private function returnResult($path = '', array $params = [], array $response = [])
    {
        if ($this->isAjax()) {
            $layout = $this->layoutFactory->create();
            $layout->initMessages();

            $response['messages'] = [$layout->getMessagesBlock()->getGroupedHtml()];
            $response['params'] = $params;
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($response);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath($path, $params);
    }

    /**
     * Define whether request is Ajax
     *
     * @return boolean
     */
    private function isAjax()
    {
        return $this->getRequest()->getParam('isAjax');
    }
}
 