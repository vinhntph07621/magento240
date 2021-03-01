<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-15
 * Time: 17:38
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Vendor\Form\Modifier;

use Omnyfy\Vendor\Api\Data\VendorAttributeInterface;
use Omnyfy\Vendor\Api\VendorAttributeGroupRepositoryInterface;
use Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface;
use Omnyfy\Vendor\Model\Locator\LocatorInterface;
use Omnyfy\Vendor\Model\Resource\Vendor\Eav\Attribute as EavAttribute;
use Omnyfy\Vendor\Model\Resource\Vendor\Eav\AttributeFactory as EavAttributeFactory;
use Magento\Eav\Api\Data\AttributeGroupInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\Translit;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Omnyfy\Vendor\Ui\DataProvider\VendorEavValidationRules;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Magento\Ui\DataProvider\Mapper\MetaProperties as MetaPropertiesMapper;
use Magento\Ui\Component\Form\Element\Wysiwyg as WysiwygElement;
use Omnyfy\Vendor\Model\Attribute\ScopeOverriddenValue;
use Magento\Framework\Locale\CurrencyInterface;

class Eav extends AbstractModifier
{
    const SORT_ORDER_MULTIPLIER = 10;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var VendorEavValidationRules
     */
    protected $vendorEavValidationRules;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var FormElementMapper
     */
    protected $formElementMapper;

    /**
     * @var MetaPropertiesMapper
     */
    protected $metaPropertiesMapper;

    /**
     * @var VendorAttributeGroupRepositoryInterface
     */
    protected $attributeGroupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var VendorAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var EavAttributeFactory
     */
    protected $eavAttributeFactory;

    /**
     * @var Translit
     */
    protected $translitFilter;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * @var array
     */
    private $attributesToDisable;

    /**
     * @var array
     */
    protected $attributesToEliminate;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var EavAttribute[]
     */
    private $attributes = [];

    /**
     * @var AttributeGroupInterface[]
     */
    private $attributeGroups = [];

    /**
     * @var array
     */
    private $canDisplayUseDefault = [];

    /**
     * @var array
     */
    private $bannedInputTypes = ['media_image'];

    /**
     * @var array
     */
    private $prevSetAttributes;

    /**
     * @var CurrencyInterface
     */
    private $localeCurrency;

    /**
     * @param LocatorInterface $locator
     * @param VendorEavValidationRules $vendorEavValidationRules
     * @param Config $eavConfig
     * @param RequestInterface $request
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param FormElementMapper $formElementMapper
     * @param MetaPropertiesMapper $metaPropertiesMapper
     * @param VendorAttributeGroupRepositoryInterface $attributeGroupRepository
     * @param VendorAttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param EavAttributeFactory $eavAttributeFactory
     * @param Translit $translitFilter
     * @param ArrayManager $arrayManager
     * @param ScopeOverriddenValue $scopeOverriddenValue
     * @param DataPersistorInterface $dataPersistor
     * @param array $attributesToDisable
     * @param array $attributesToEliminate
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        LocatorInterface $locator,
        VendorEavValidationRules $vendorEavValidationRules,
        Config $eavConfig,
        RequestInterface $request,
        GroupCollectionFactory $groupCollectionFactory,
        StoreManagerInterface $storeManager,
        FormElementMapper $formElementMapper,
        MetaPropertiesMapper $metaPropertiesMapper,
        VendorAttributeGroupRepositoryInterface $attributeGroupRepository,
        VendorAttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        EavAttributeFactory $eavAttributeFactory,
        Translit $translitFilter,
        ArrayManager $arrayManager,
        ScopeOverriddenValue $scopeOverriddenValue,
        DataPersistorInterface $dataPersistor,
        $attributesToDisable = [],
        $attributesToEliminate = []
    ) {
        $this->locator = $locator;
        $this->vendorEavValidationRules = $vendorEavValidationRules;
        $this->eavConfig = $eavConfig;
        $this->request = $request;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->storeManager = $storeManager;
        $this->formElementMapper = $formElementMapper;
        $this->metaPropertiesMapper = $metaPropertiesMapper;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeRepository = $attributeRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->translitFilter = $translitFilter;
        $this->arrayManager = $arrayManager;
        $this->scopeOverriddenValue = $scopeOverriddenValue;
        $this->dataPersistor = $dataPersistor;
        $this->attributesToDisable = $attributesToDisable;
        $this->attributesToEliminate = $attributesToEliminate;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $sortOrder = 0;

        foreach ($this->getGroups() as $groupCode => $group) {
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];

            if ($attributes) {
                $meta[$groupCode]['children'] = $this->getAttributesMeta($attributes, $groupCode);
                $meta[$groupCode]['arguments']['data']['config']['componentType'] = Fieldset::NAME;
                $meta[$groupCode]['arguments']['data']['config']['label'] = __($group->getAttributeGroupName());
                $meta[$groupCode]['arguments']['data']['config']['collapsible'] = true;
                $meta[$groupCode]['arguments']['data']['config']['dataScope'] = self::DATA_SCOPE_PRODUCT;
                $meta[$groupCode]['arguments']['data']['config']['sortOrder'] =
                    $sortOrder * self::SORT_ORDER_MULTIPLIER;
                $meta[$groupCode]['arguments']['data']['config']['opened'] = true;
            }

            $sortOrder++;
        }

        return $meta;
    }

    /**
     * Get attributes meta
     *
     * @param array $attributes
     * @param string $groupCode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributesMeta(array $attributes, $groupCode)
    {
        $meta = [];

        foreach ($attributes as $sortOrder => $attribute) {
            if (in_array($attribute->getFrontendInput(), $this->bannedInputTypes)) {
                continue;
            }

            if (in_array($attribute->getAttributeCode(), $this->attributesToEliminate)) {
                continue;
            }
            if (!($attributeContainer = $this->setupAttributeContainerMeta($attribute))) {
                continue;
            }

            $attributeContainer = $this->addContainerChildren($attributeContainer, $attribute, $groupCode, $sortOrder);

            $meta[static::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $attributeContainer;
        }

        return $meta;
    }

    /**
     * Add container children
     *
     * @param array $attributeContainer
     * @param $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     * @api
     */
    public function addContainerChildren(
        array $attributeContainer,
        $attribute,
        $groupCode,
        $sortOrder
    ) {
        foreach ($this->getContainerChildren($attribute, $groupCode, $sortOrder) as $childCode => $child) {
            $attributeContainer['children'][$childCode] = $child;
        }

        $attributeContainer = $this->arrayManager->merge(
            ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER),
            $attributeContainer,
            [
                'sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER,
                // TODO: Eliminate this in scope of MAGETWO-51364
                'scopeLabel' => $this->getScopeLabel($attribute),
            ]
        );

        return $attributeContainer;
    }

    /**
     * Retrieve container child fields
     *
     * @param  $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     * @api
     */
    public function getContainerChildren( $attribute, $groupCode, $sortOrder)
    {
        if (!($child = $this->setupAttributeMeta($attribute, $groupCode, $sortOrder))) {
            return [];
        }

        return [$attribute->getAttributeCode() => $child];
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if (!$this->locator->getVendor()->getId() && $this->dataPersistor->get('omnyfy_vendor_vendor')) {
            return $this->resolvePersistentData($data);
        }

        $vendorId = $this->locator->getVendor()->getId();

        /** @var string $groupCode */
        foreach (array_keys($this->getGroups()) as $groupCode) {
            /** @var VendorAttributeInterface[] $attributes */
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];

            foreach ($attributes as $attribute) {
                if (null !== ($attributeValue = $this->setupAttributeData($attribute))) {
                    if ($attribute->getFrontendInput() === 'price' && is_scalar($attributeValue)) {
                        $attributeValue = $this->formatPrice($attributeValue);
                    }
                    if (('image' === $attribute->getFrontendInput() || 'media_image' === $attribute->getFrontendInput())
                        && is_scalar($attributeValue)) {
                        $attributeValue = $this->prepareImageValue($attributeValue, $attribute->getAttributeCode());
                    }
                    $data[$vendorId][self::DATA_SOURCE_DEFAULT][$attribute->getAttributeCode()] = $attributeValue;
                }
            }
        }

        $data['id'] = $vendorId;
        $data[$vendorId][self::DATA_SOURCE_DEFAULT]['id'] = $vendorId;

        return $data;
    }

    /**
     * Resolve data persistence
     *
     * @param array $data
     * @return array
     */
    private function resolvePersistentData(array $data)
    {
        $persistentData = (array)$this->dataPersistor->get('omnyfy_vendor_vendor');
        $this->dataPersistor->clear('omnyfy_vendor_vendor');
        $vendorId = $this->locator->getVendor()->getId();

        if (empty($data[$vendorId][self::DATA_SOURCE_DEFAULT])) {
            $data[$vendorId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$vendorId] = array_replace_recursive(
            $data[$vendorId][self::DATA_SOURCE_DEFAULT],
            $persistentData
        );

        return $data;
    }

    /**
     * Get vendor type
     *
     * @return null|string
     */
    private function getVendorType()
    {
        return (string)$this->request->getParam('type', $this->locator->getVendor()->getTypeId());
    }

    /**
     * Return prev set id
     *
     * @return int
     */
    private function getPreviousSetId()
    {
        return (int)$this->request->getParam('prev_set_id', 0);
    }

    /**
     * Retrieve groups
     *
     * @return AttributeGroupInterface[]
     */
    private function getGroups()
    {
        if (!$this->attributeGroups) {
            $searchCriteria = $this->prepareGroupSearchCriteria()->create();
            $attributeGroupSearchResult = $this->attributeGroupRepository->getList($searchCriteria);
            foreach ($attributeGroupSearchResult->getItems() as $group) {
                $this->attributeGroups[$this->calculateGroupCode($group)] = $group;
            }
        }

        return $this->attributeGroups;
    }

    /**
     * Initialize attribute group search criteria with filters.
     *
     * @return SearchCriteriaBuilder
     */
    private function prepareGroupSearchCriteria()
    {
        return $this->searchCriteriaBuilder->addFilter(
            AttributeGroupInterface::ATTRIBUTE_SET_ID,
            $this->getAttributeSetId()
        );
    }

    /**
     * Return current attribute set id
     *
     * @return int|null
     */
    private function getAttributeSetId()
    {
        return $this->locator->getVendor()->getAttributeSetId();
    }

    /**
     * Retrieve attributes
     *
     * @return VendorAttributeInterface[]
     */
    private function getAttributes()
    {
        if (!$this->attributes) {
            foreach ($this->getGroups() as $group) {
                $this->attributes[$this->calculateGroupCode($group)] = $this->loadAttributes($group);
            }
        }

        return $this->attributes;
    }

    /**
     * Loading vendor attributes from group
     *
     * @param AttributeGroupInterface $group
     * @return VendorAttributeInterface[]
     */
    private function loadAttributes(AttributeGroupInterface $group)
    {
        $attributes = [];
        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setAscendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(AttributeGroupInterface::GROUP_ID, $group->getAttributeGroupId())
            ->addFilter(VendorAttributeInterface::IS_VISIBLE, 1)
            ->addSortOrder($sortOrder)
            ->create();
        $groupAttributes = $this->attributeRepository->getList($searchCriteria)->getItems();
        //$vendorType = $this->getVendorType();
        foreach ($groupAttributes as $attribute) {
            //$applyTo = $attribute->getApplyTo();
            //$isRelated = !$applyTo || in_array($vendorType, $applyTo);
            //if ($isRelated) {
                $attributes[] = $attribute;
            //}
        }

        return $attributes;
    }

    /**
     * Get attribute codes of prev set
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPreviousSetAttributes()
    {
        if ($this->prevSetAttributes === null) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('attribute_set_id', $this->getPreviousSetId())
                ->create();
            $attributes = $this->attributeRepository->getList($searchCriteria)->getItems();
            $this->prevSetAttributes = [];
            foreach ($attributes as $attribute) {
                $this->prevSetAttributes[] = $attribute->getAttributeCode();
            }
        }

        return $this->prevSetAttributes;
    }

    /**
     * Check is product already new or we trying to create one.
     *
     * @return bool
     */
    private function isVendorExists()
    {
        return (bool) $this->locator->getVendor()->getId();
    }

    /**
     * Initial meta setup
     *
     * @param $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @api
     */
    public function setupAttributeMeta($attribute, $groupCode, $sortOrder)
    {
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);

        $meta = $this->arrayManager->set($configPath, [], [
            'dataType' => $attribute->getFrontendInput(),
            'formElement' => $this->getFormElementsMapValue($attribute->getFrontendInput()),
            'visible' => $attribute->getIsVisible(),
            'required' => $attribute->getIsRequired(),
            'notice' => $attribute->getNote(),
            'default' => (!$this->isVendorExists()) ? $attribute->getDefaultValue() : null,
            'label' => __($attribute->getDefaultFrontendLabel()),
            'code' => $attribute->getAttributeCode(),
            'source' => $groupCode,
            'scopeLabel' => $this->getScopeLabel($attribute),
            'globalScope' => $this->isScopeGlobal($attribute),
            'sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER,
            'tooltip' => $attribute->getToolTip() ? [
                'description' => $attribute->getToolTip()
            ] : false,
        ]);

        // TODO: Refactor to $attribute->getOptions() when MAGETWO-48289 is done
        $attributeModel = $this->getAttributeModel($attribute);
        if ($attributeModel->usesSource()) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'options' => $attributeModel->getSource()->getAllOptions(),
            ]);
        }

        if ($this->canDisplayUseDefault($attribute)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'service' => [
                    'template' => 'ui/form/element/helper/service',
                ]
            ]);
        }

        if (!$this->arrayManager->exists($configPath . '/componentType', $meta)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'componentType' => Field::NAME,
            ]);
        }

        if (in_array($attribute->getAttributeCode(), $this->attributesToDisable)) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'disabled' => true,
            ]);
        }

        // TODO: getAttributeModel() should not be used when MAGETWO-48284 is complete
        $childData = $this->arrayManager->get($configPath, $meta, []);
        if (($rules = $this->vendorEavValidationRules->build($this->getAttributeModel($attribute), $childData))) {
            $meta = $this->arrayManager->merge($configPath, $meta, [
                'validation' => $rules,
            ]);
        }

        $meta = $this->addUseDefaultValueCheckbox($attribute, $meta);

        switch ($attribute->getFrontendInput()) {
            case 'boolean':
                $meta = $this->customizeCheckbox($attribute, $meta);
                break;
            case 'textarea':
                $meta = $this->customizeWysiwyg($attribute, $meta);
                break;
            case 'price':
                $meta = $this->customizePriceAttribute($attribute, $meta);
                break;
            case 'media_image':
            case 'image':
                $meta = $this->customiseImageAttribute($attribute, $meta);
                break;
            case 'gallery':
                // Gallery attribute is being handled by "Images And Videos" section
                $meta = [];
                break;
        }

        return $meta;
    }

    /**
     * @param $attribute
     * @param array $meta
     * @return array
     */
    private function addUseDefaultValueCheckbox($attribute, array $meta)
    {
        $canDisplayService = $this->canDisplayUseDefault($attribute);
        if ($canDisplayService) {
            $meta['arguments']['data']['config']['service'] = [
                'template' => 'ui/form/element/helper/service',
            ];

            $meta['arguments']['data']['config']['disabled'] = !$this->scopeOverriddenValue->containsValue(
                \Omnyfy\Vendor\Api\Data\VendorInterface::class,
                $this->locator->getVendor(),
                $attribute->getAttributeCode(),
                $this->locator->getStore()->getId()
            );
        }
        return $meta;
    }

    /**
     * Setup attribute container meta
     *
     * @param $attribute
     * @return array
     * @api
     */
    public function setupAttributeContainerMeta($attribute)
    {
        $containerMeta = $this->arrayManager->set(
            'arguments/data/config',
            [],
            [
                'formElement' => 'container',
                'componentType' => 'container',
                'breakLine' => false,
                'label' => __($attribute->getDefaultFrontendLabel()),
                'required' => $attribute->getIsRequired(),
            ]
        );

        if ($attribute->getIsWysiwygEnabled()) {
            $containerMeta = $this->arrayManager->merge(
                'arguments/data/config',
                $containerMeta,
                [
                    'component' => 'Magento_Ui/js/form/components/group'
                ]
            );
        }

        return $containerMeta;
    }

    /**
     * Setup attribute data
     *
     * @param $attribute
     * @return mixed|null
     * @api
     */
    public function setupAttributeData($attribute)
    {
        $vendor = $this->locator->getVendor();
        $vendorId = $vendor->getId();
        $prevSetId = $this->getPreviousSetId();
        $notUsed = !$prevSetId
            || ($prevSetId && !in_array($attribute->getAttributeCode(), $this->getPreviousSetAttributes()));

        if ($vendorId && $notUsed) {
            return $this->getValue($attribute);
        }

        return null;
    }

    private function customiseImageAttribute($attribute, array $meta)
    {
        if ('image' === $attribute->getFrontendInput() || 'media_image' == $attribute->getFrontendInput()) {
            $meta['arguments']['data']['config']['dataType'] = 'string';
            $meta['arguments']['data']['config']['formElement'] = 'fileUploader';
            $meta['arguments']['data']['config']['elementTmpl'] = 'Omnyfy_Vendor/uploader';
            $meta['arguments']['data']['config']['previewTmpl'] = 'Magento_Catalog/image-preview';
            if ('banner' == $attribute->getAttributeCode()) {
                $meta['arguments']['data']['config']['uploaderConfig'] = ['url' => 'omnyfy_vendor/vendor_store_upload/banner'];
            }
            elseif ('logo' == $attribute->getAttributeCode()) {
                $meta['arguments']['data']['config']['uploaderConfig'] = ['url' => 'omnyfy_vendor/vendor_store_upload/logo'];
            }
            else {
                $meta['arguments']['data']['config']['previewTmpl'] = 'Omnyfy_Vendor/media-preview';
                $meta['arguments']['data']['config']['uploaderConfig'] = ['url' => 'omnyfy_vendor/vendor_store_upload/media'];
            }
        }

        return $meta;
    }

    /**
     * Customize checkboxes
     *
     * @param $attribute
     * @param array $meta
     * @return array
     */
    private function customizeCheckbox($attribute, array $meta)
    {
        if ($attribute->getFrontendInput() === 'boolean') {
            $meta['arguments']['data']['config']['prefer'] = 'toggle';
            $meta['arguments']['data']['config']['valueMap'] = [
                'true' => '1',
                'false' => '0',
            ];
        }

        return $meta;
    }

    /**
     * Customize attribute that has price type
     *
     * @param $attribute
     * @param array $meta
     * @return array
     */
    private function customizePriceAttribute($attribute, array $meta)
    {
        if ($attribute->getFrontendInput() === 'price') {
            $meta['arguments']['data']['config']['addbefore'] = $this->locator->getStore()
                ->getBaseCurrency()
                ->getCurrencySymbol();
        }

        return $meta;
    }

    /**
     * Add wysiwyg properties
     *
     * @param $attribute
     * @param array $meta
     * @return array
     */
    private function customizeWysiwyg($attribute, array $meta)
    {
        if (!$attribute->getIsWysiwygEnabled()) {
            return $meta;
        }

        $meta['arguments']['data']['config']['formElement'] = WysiwygElement::NAME;
        $meta['arguments']['data']['config']['wysiwyg'] = true;
        $meta['arguments']['data']['config']['wysiwygConfigData'] = [
            'add_variables' => false,
            'add_widgets' => false,
            'add_directives' => true,
            'use_container' => true,
            'container_class' => 'hor-scroll',
        ];

        return $meta;
    }

    /**
     * Retrieve form element
     *
     * @param string $value
     * @return mixed
     */
    private function getFormElementsMapValue($value)
    {
        $valueMap = $this->formElementMapper->getMappings();

        return isset($valueMap[$value]) ? $valueMap[$value] : $value;
    }

    /**
     * Retrieve attribute value
     *
     * @param $attribute
     * @return mixed
     */
    private function getValue($attribute)
    {
        /** @var Vendor $vendor */
        $vendor = $this->locator->getVendor();

        return $vendor->getData($attribute->getAttributeCode());
    }

    /**
     * Retrieve scope label
     *
     * @param $attribute
     * @return \Magento\Framework\Phrase|string
     */
    private function getScopeLabel($attribute)
    {
        if (
            $this->storeManager->isSingleStoreMode()
            || $attribute->getFrontendInput() === AttributeInterface::FRONTEND_INPUT
        ) {
            return '';
        }

        switch ($attribute->getScope()) {
            case VendorAttributeInterface::SCOPE_GLOBAL_TEXT:
                return __('[GLOBAL]');
            case VendorAttributeInterface::SCOPE_WEBSITE_TEXT:
                return __('[WEBSITE]');
            case VendorAttributeInterface::SCOPE_STORE_TEXT:
                return __('[STORE VIEW]');
        }

        return '';
    }

    /**
     * Whether attribute can have default value
     *
     * @param $attribute
     * @return bool
     */
    private function canDisplayUseDefault($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        /** @var Vendor $vendor */
        $vendor = $this->locator->getVendor();

        if (isset($this->canDisplayUseDefault[$attributeCode])) {
            return $this->canDisplayUseDefault[$attributeCode];
        }

        return $this->canDisplayUseDefault[$attributeCode] = (
            ($attribute->getScope() != VendorAttributeInterface::SCOPE_GLOBAL_TEXT)
            && $vendor
            && $vendor->getId()
            && $vendor->getStoreId()
        );
    }

    /**
     * Check if attribute scope is global.
     *
     * @param $attribute
     * @return bool
     */
    private function isScopeGlobal($attribute)
    {
        return $attribute->getScope() === VendorAttributeInterface::SCOPE_GLOBAL_TEXT;
    }

    /**
     * Load attribute model by attribute data object.
     *
     * TODO: This method should be eliminated when all missing service methods are implemented
     *
     * @param $attribute
     * @return EavAttribute
     */
    private function getAttributeModel($attribute)
    {
        return $this->eavAttributeFactory->create()->load($attribute->getAttributeId());
    }

    /**
     * Calculate group code based on group name.
     *
     * TODO: This logic is copy-pasted from \Magento\Eav\Model\Entity\Attribute\Group::beforeSave
     * TODO: and should be moved to a separate service, which will allow two-way conversion groupName <=> groupCode
     * TODO: Remove after MAGETWO-48290 is complete
     *
     * @param AttributeGroupInterface $group
     * @return string
     */
    private function calculateGroupCode(AttributeGroupInterface $group)
    {
        $attributeGroupCode = $group->getAttributeGroupCode();

        return $attributeGroupCode;
    }

    /**
     * The getter function to get the locale currency for real application code
     *
     * @return \Magento\Framework\Locale\CurrencyInterface
     *
     * @deprecated
     */
    private function getLocaleCurrency()
    {
        if ($this->localeCurrency === null) {
            $this->localeCurrency = \Magento\Framework\App\ObjectManager::getInstance()->get(CurrencyInterface::class);
        }
        return $this->localeCurrency;
    }

    /**
     * Format price according to the locale of the currency
     *
     * @param mixed $value
     * @return string
     */
    protected function formatPrice($value)
    {
        if (!is_numeric($value)) {
            return null;
        }

        $store = $this->storeManager->getStore();
        $currency = $this->getLocaleCurrency()->getCurrency($store->getBaseCurrencyCode());
        $value = $currency->toCurrency($value, ['display' => \Magento\Framework\Currency::NO_SYMBOL]);

        return $value;
    }

    protected function prepareImageValue($value, $code='')
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );

        $path = 'media';
        switch(strtolower($code)) {
            case 'logo':
                $path = 'logo';
                break;
            case 'banner':
                $path = 'banner';
                break;
        }

        $value = str_replace(\Omnyfy\Vendor\Api\Data\VendorInterface::BASE_BANNER_PATH, '', $value);
        $value = str_replace(\Omnyfy\Vendor\Api\Data\VendorInterface::BASE_LOGO_PATH, '', $value);
        $value = str_replace(\Omnyfy\Vendor\Api\Data\VendorInterface::BASE_MEDIA_PATH, '', $value);

        return [0=>[
            'name' => $value,
            'url' => $baseUrl . 'omnyfy/vendor/' . $path . '/' . $value
        ]];
    }
}
