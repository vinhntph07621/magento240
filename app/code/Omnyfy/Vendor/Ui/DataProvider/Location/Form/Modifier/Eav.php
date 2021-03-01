<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-23
 * Time: 16:37
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Location\Form\Modifier;

use Omnyfy\Vendor\Api\Data\LocationAttributeInterface;
use Omnyfy\Vendor\Api\LocationAttributeGroupRepositoryInterface;
use Omnyfy\Vendor\Api\LocationAttributeRepositoryInterface;
use Omnyfy\Vendor\Model\Locator\LocationLocatorInterface;
use Omnyfy\Vendor\Model\Resource\Eav\Attribute as EavAttribute;
use Omnyfy\Vendor\Model\Resource\Eav\AttributeFactory as EavAttributeFactory;
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
use Omnyfy\Vendor\Ui\DataProvider\Location\EavValidationRules;
use Magento\Ui\DataProvider\Mapper\FormElement as FormElementMapper;
use Magento\Ui\DataProvider\Mapper\MetaProperties as MetaPropertiesMapper;
use Magento\Ui\Component\Form\Element\Wysiwyg as WysiwygElement;
use Omnyfy\Vendor\Model\Attribute\ScopeOverriddenValue;
use Magento\Framework\Locale\CurrencyInterface;
use Omnyfy\Vendor\Api\VendorTypeRepositoryInterface;
use Magento\Backend\Model\Session as BackendSession;

class Eav extends AbstractModifier
{
    const SORT_ORDER_MULTIPLIER = 10;

    /**
     * @var LocationLocatorInterface
     */
    protected $locator;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var EavValidationRules
     */
    protected $locationEavValidationRules;

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
     * @var LocationAttributeGroupRepositoryInterface
     */
    protected $attributeGroupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var LocationAttributeRepositoryInterface
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
     * @var VendorTypeRepositoryInterface
     */
    protected $vendorTypeRepository;

    /**
     * @var BackendSession
     */
    protected $_session;

    protected $registry;

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

    private $defaultAttributeSetId = null;

    /**
     * Eav constructor.
     * @param LocationLocatorInterface $locator
     * @param EavValidationRules $locationEavValidationRules
     * @param Config $eavConfig
     * @param RequestInterface $request
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param FormElementMapper $formElementMapper
     * @param MetaPropertiesMapper $metaPropertiesMapper
     * @param LocationAttributeGroupRepositoryInterface $attributeGroupRepository
     * @param LocationAttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param EavAttributeFactory $eavAttributeFactory
     * @param Translit $translitFilter
     * @param ArrayManager $arrayManager
     * @param ScopeOverriddenValue $scopeOverriddenValue
     * @param DataPersistorInterface $dataPersistor
     * @param VendorTypeRepositoryInterface $vendorTypeRepository
     * @param BackendSession $session
     * @param array $attributesToDisable
     * @param array $attributesToEliminate
     */
    public function __construct(
        LocationLocatorInterface $locator,
        EavValidationRules $locationEavValidationRules,
        Config $eavConfig,
        RequestInterface $request,
        GroupCollectionFactory $groupCollectionFactory,
        StoreManagerInterface $storeManager,
        FormElementMapper $formElementMapper,
        MetaPropertiesMapper $metaPropertiesMapper,
        LocationAttributeGroupRepositoryInterface $attributeGroupRepository,
        LocationAttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        EavAttributeFactory $eavAttributeFactory,
        Translit $translitFilter,
        ArrayManager $arrayManager,
        ScopeOverriddenValue $scopeOverriddenValue,
        DataPersistorInterface $dataPersistor,
        VendorTypeRepositoryInterface $vendorTypeRepository,
        BackendSession $session,
        $attributesToDisable = [],
        $attributesToEliminate = []
    ){
        $this->locator = $locator;
        $this->locationEavValidationRules = $locationEavValidationRules;
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
        $this->vendorTypeRepository = $vendorTypeRepository;
        $this->_session = $session;
    }

    public function modifyMeta(array $meta)
    {
        $location = $this->locator->getLocation();
        if (empty($location->getId())) {
            $vendorInfo = $this->_session->getVendorInfo();

            if (empty($vendorInfo) || !isset($vendorInfo['type_id']) || empty($vendorInfo['type_id'])) {
                return $meta;
            }

            if (empty($this->defaultAttributeSetId)) {
                $vendorType = $this->vendorTypeRepository->getById($vendorInfo['type_id']);
                $this->defaultAttributeSetId = $vendorType->getLocationAttributeSetId();
            }
        }

        $sortOrder = 0;

        foreach ($this->getGroups() as $groupCode => $group) {
            $attributes = !empty($this->getAttributes()[$groupCode]) ? $this->getAttributes()[$groupCode] : [];

            if ($attributes) {
                $meta[$groupCode]['children'] = $this->getAttributesMeta($attributes, $groupCode);
                $meta[$groupCode]['arguments']['data']['config']['componentType'] = Fieldset::NAME;
                $meta[$groupCode]['arguments']['data']['config']['label'] = __($group->getAttributeGroupName());
                $meta[$groupCode]['arguments']['data']['config']['collapsible'] = false;
                $meta[$groupCode]['arguments']['data']['config']['dataScope'] = self::DATA_SCOPE_PRODUCT;
                $meta[$groupCode]['arguments']['data']['config']['sortOrder'] =
                    $sortOrder * self::SORT_ORDER_MULTIPLIER;
                $meta[$groupCode]['arguments']['data']['config']['opened'] = true;
            }

            $sortOrder++;
        }

        return $meta;
    }

    public function modifyData(array $data)
    {
        if (!$this->locator->getLocation()->getId() && $this->dataPersistor->get('omnyfy_vendor_location')) {
            return $this->resolvePersistentData($data);
        }

        $locationId = $this->locator->getLocation()->getId();
        if (empty($locationId)) {
            $vendorInfo = $this->_session->getVendorInfo();
            if (empty($vendorInfo) || !isset($vendorInfo['type_id']) || empty($vendorInfo['type_id'])) {
                return $data;
            }

            if (empty($this->defaultAttributeSetId)) {
                $vendorType = $this->vendorTypeRepository->getById($vendorInfo['type_id']);
                $this->defaultAttributeSetId = $vendorType->getLocationAttributeSetId();
            }
        }

        /** @var string $groupCode */
        foreach (array_keys($this->getGroups()) as $groupCode) {
            /** @var LocationAttributeInterface[] $attributes */
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
                    $data[$locationId][self::DATA_SOURCE_DEFAULT][$attribute->getAttributeCode()] = $attributeValue;
                }
            }
        }

        $data['id'] = $locationId;
        $data[$locationId][self::DATA_SOURCE_DEFAULT]['id'] = $locationId;

        return $data;
    }

    /**
     * Get attributes meta
     *
     * @param $attributes
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

            //if (!($attributeContainer = $this->setupAttributeContainerMeta($attribute))) {
            //    continue;
            //}

            //$attributeContainer = $this->addContainerChildren($meta[$groupCode], $attribute, $groupCode, $sortOrder);

            //$meta[static::CONTAINER_PREFIX . $attribute->getAttributeCode()] = $attributeContainer;

            $meta[$attribute->getAttributeCode()] = $this->setupAttributeMeta($attribute, $groupCode, $sortOrder);
        }

        return $meta;
    }

    /**
     * Add container children
     *
     * @param array $attributeContainer
     * @param  $attribute
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
     * @param $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     * @api
     */
    public function getContainerChildren($attribute, $groupCode, $sortOrder)
    {
        if (!($child = $this->setupAttributeMeta($attribute, $groupCode, $sortOrder))) {
            return [];
        }

        return [$attribute->getAttributeCode() => $child];
    }

    /**
     * Resolve data persistence
     *
     * @param array $data
     * @return array
     */
    private function resolvePersistentData(array $data)
    {
        $persistentData = (array)$this->dataPersistor->get('omnyfy_vendor_location');
        $this->dataPersistor->clear('omnyfy_vendor_location');
        $locationId = $this->locator->getLocation()->getId();

        if (empty($data[$locationId][self::DATA_SOURCE_DEFAULT])) {
            $data[$locationId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$locationId] = array_replace_recursive(
            $data[$locationId][self::DATA_SOURCE_DEFAULT],
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
        return (string)$this->request->getParam('type', $this->locator->getLocation()->getVendorTypeId());
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
        $attributeSetId = $this->locator->getLocation()->getAttributeSetId();

        return empty($attributeSetId) ? $this->defaultAttributeSetId : $attributeSetId;
    }

    /**
     * Retrieve attributes
     *
     * @return LocationAttributeInterface[]
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
     * @return LocationAttributeInterface[]
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
            ->addFilter(LocationAttributeInterface::IS_VISIBLE, 1)
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
     * Check is location already new or we trying to create one.
     *
     * @return bool
     */
    private function isLocationExists()
    {
        return (bool) $this->locator->getLocation()->getId();
    }

    /**
     * Initial meta setup
     *
     * @param  $attribute
     * @param string $groupCode
     * @param int $sortOrder
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @api
     */
    public function setupAttributeMeta( $attribute, $groupCode, $sortOrder)
    {
        $configPath = ltrim(static::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);

        $meta = $this->arrayManager->set($configPath, [], [
            'dataType' => $attribute->getFrontendInput(),
            'formElement' => $this->getFormElementsMapValue($attribute->getFrontendInput()),
            'visible' => $attribute->getIsVisible(),
            'required' => $attribute->getIsRequired(),
            'notice' => $attribute->getNote(),
            'default' => (!$this->isLocationExists()) ? $attribute->getDefaultValue() : null,
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
        if (($rules = $this->locationEavValidationRules->build($this->getAttributeModel($attribute), $childData))) {
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
                \Omnyfy\Vendor\Api\Data\LocationInterface::class,
                $this->locator->getLocation(),
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
        $location = $this->locator->getLocation();
        $locationId = $location->getId();
        $prevSetId = $this->getPreviousSetId();
        $notUsed = !$prevSetId
            || ($prevSetId && !in_array($attribute->getAttributeCode(), $this->getPreviousSetAttributes()));

        if ($locationId && $notUsed) {
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

            if ('logo' == $attribute->getAttributeCode()) {
                $meta['arguments']['data']['config']['uploaderConfig'] = ['url' => 'omnyfy_vendor/location_upload/logo'];
            }
            else {
                $meta['arguments']['data']['config']['previewTmpl'] = 'Omnyfy_Vendor/media-preview';
                $meta['arguments']['data']['config']['uploaderConfig'] = ['url' => 'omnyfy_vendor/location_upload/media'];
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
        /** @var Location $location */
        $location = $this->locator->getLocation();

        return $location->getData($attribute->getAttributeCode());
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
            case LocationAttributeInterface::SCOPE_GLOBAL_TEXT:
                return __('[GLOBAL]');
            case LocationAttributeInterface::SCOPE_WEBSITE_TEXT:
                return __('[WEBSITE]');
            case LocationAttributeInterface::SCOPE_STORE_TEXT:
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
        /** @var Location $location */
        $location = $this->locator->getLocation();

        if (isset($this->canDisplayUseDefault[$attributeCode])) {
            return $this->canDisplayUseDefault[$attributeCode];
        }

        return $this->canDisplayUseDefault[$attributeCode] = (
            ($attribute->getScope() != LocationAttributeInterface::SCOPE_GLOBAL_TEXT)
            && $location
            && $location->getId()
            && $location->getStoreId()
        );
    }

    /**
     * Check if attribute scope is global.
     *
     * @param  $attribute
     * @return bool
     */
    private function isScopeGlobal($attribute)
    {
        return $attribute->getScope() === LocationAttributeInterface::SCOPE_GLOBAL_TEXT;
    }

    /**
     * Load attribute model by attribute data object.
     *
     * TODO: This method should be eliminated when all missing service methods are implemented
     *
     * @param LocationAttributeInterface $attribute
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

    protected function prepareImageValue($value, $code = null)
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

        $value = str_replace(\Omnyfy\Vendor\Api\Data\LocationInterface::BASE_BANNER_PATH, '', $value);
        $value = str_replace(\Omnyfy\Vendor\Api\Data\LocationInterface::BASE_LOGO_PATH, '', $value);
        $value = str_replace(\Omnyfy\Vendor\Api\Data\LocationInterface::BASE_MEDIA_PATH, '', $value);

        return [0=>[
            'name' => $value,
            'url' => $baseUrl . 'omnyfy/location/' . $path . '/' . $value
        ]];
    }
}
