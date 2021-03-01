<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 11/9/17
 * Time: 12:06 PM
 */
namespace Omnyfy\Vendor\Model\Resource\Vendor\Eav;

use Magento\Catalog\Model\Attribute\LockValidatorInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class Attribute extends \Magento\Eav\Model\Entity\Attribute implements
    \Omnyfy\Vendor\Api\Data\VendorAttributeInterface, \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface
{
    const MODULE_NAME = 'Omnyfy_Vendor';

    const ENTITY = 'omnyfy_vendor_eav_attribute';

    const KEY_IS_GLOBAL = 'is_global';

    protected $attrLockValidator;

    protected $_eventObject = 'attribute';

    protected static $_labels = null;

    protected $_eventPrefix = 'omnyfy_vendor_vendor_attribute';

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Indexer
     */
    protected $_vendorFlatIndexerProcessor;

    /**
     * @var \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer
     */
    protected $_vendorFlatIndexerHelper;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Processor
     */
    protected $_indexerEavProcessor;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionDataFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        DateTimeFormatterInterface $dateTimeFormatter,
        \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Processor $vendorFlatIndexerProcessor,
        \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Processor $indexerEavProcessor,
        \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $vendorFlatIndexerHelper,
        LockValidatorInterface $lockValidator,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->_indexerEavProcessor = $indexerEavProcessor;
        $this->_vendorFlatIndexerProcessor = $vendorFlatIndexerProcessor;
        $this->_vendorFlatIndexerHelper = $vendorFlatIndexerHelper;
        $this->attrLockValidator = $lockValidator;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $optionDataFactory,
            $dataObjectProcessor,
            $dataObjectHelper,
            $localeDate,
            $reservedAttributeList,
            $localeResolver,
            $dateTimeFormatter,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\Vendor\Attribute');
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeSave()
    {
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData[self::KEY_IS_GLOBAL])) {
            if (!isset($this->_data[self::KEY_IS_GLOBAL])) {
                $this->_data[self::KEY_IS_GLOBAL] = self::SCOPE_GLOBAL;
            }
            if ($this->_data[self::KEY_IS_GLOBAL] != $this->_origData[self::KEY_IS_GLOBAL]) {
                try {
                    $this->attrLockValidator->validate($this);
                } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Do not change the scope. %1', $exception->getMessage())
                    );
                }
            }
        }
        if ($this->getFrontendInput() == 'textarea') {
            if ($this->getIsWysiwygEnabled()) {
                $this->setIsHtmlAllowedOnFront(1);
            }
        }
        if (!$this->getIsSearchable()) {
            $this->setIsVisibleInAdvancedSearch(false);
        }
        return parent::beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        $this->_eavConfig->clear();

        //TODO: hook index
        /*
        if ($this->_isOriginalEnabledInFlat() != $this->_isEnabledInFlat()) {
            $this->_vendorFlatIndexerProcessor->markIndexerAsInvalid();
        }
        if ($this->_isOriginalIndexable() !== $this->isIndexable()
            || ($this->isIndexable() && $this->dataHasChangedFor(self::KEY_IS_GLOBAL))
        ) {
            $this->_indexerEavProcessor->markIndexerAsInvalid();
        }
        */

        return parent::afterSave();
    }

    /**
     * Is attribute enabled for flat indexing
     *
     * @return bool
     */
    protected function _isEnabledInFlat()
    {
        return $this->getData('backend_type') == 'static'
            || $this->_vendorFlatIndexerHelper->isAddFilterableAttributes()
            && $this->getData('is_filterable') > 0
            || $this->getData('used_in_listing') == 1
            || $this->getData('used_for_sort_by') == 1;
    }

    /**
     * Is original attribute enabled for flat indexing
     *
     * @return bool
     */
    protected function _isOriginalEnabledInFlat()
    {
        return $this->getOrigData('backend_type') == 'static'
            || $this->_vendorFlatIndexerHelper->isAddFilterableAttributes()
            && $this->getOrigData('is_filterable') > 0
            || $this->getOrigData('used_in_listing') == 1
            || $this->getOrigData('used_for_sort_by') == 1;
    }

    /**
     * Register indexing event before delete catalog eav attribute
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        $this->attrLockValidator->validate($this);
        return parent::beforeDelete();
    }

    /**
     * Init indexing process after catalog eav attribute delete commit
     *
     * @return $this
     */
    public function afterDeleteCommit()
    {
        parent::afterDeleteCommit();

        //TODO: hook index
        /*
        if ($this->_isOriginalEnabledInFlat()) {
            $this->_vendorFlatIndexerProcessor->markIndexerAsInvalid();
        }
        if ($this->_isOriginalIndexable()) {
            $this->_indexerEavProcessor->markIndexerAsInvalid();
        }
        */
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return $this->_getData(self::KEY_IS_GLOBAL);
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Retrieve source model
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Whether allowed for rule condition
     *
     * @return bool
     */
    public function isAllowedForRuleCondition()
    {
        $allowedInputTypes = [
            'boolean',
            'date',
            'datetime',
            'multiselect',
            'price',
            'select',
            'text',
            'textarea',
            'weight',
        ];
        return $this->getIsVisible() && in_array($this->getFrontendInput(), $allowedInputTypes);
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'Magento\Eav\Model\Entity\Attribute\Source\Table';
    }

    /**
     * Check is an attribute used in EAV index
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isIndexable()
    {
        if (!$this->getIsFilterableInSearch() && !$this->getIsVisibleInAdvancedSearch() && !$this->getIsFilterable()) {
            return false;
        }

        $backendType = $this->getBackendType();
        $frontendInput = $this->getFrontendInput();

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } elseif ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } elseif ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Is original attribute config indexable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _isOriginalIndexable()
    {
        if (!$this->getOrigData('is_filterable_in_search')
            && !$this->getOrigData('is_visible_in_advanced_search')
            && !$this->getOrigData('is_filterable')) {
            return false;
        }

        $backendType = $this->getOrigData('backend_type');
        $frontendInput = $this->getOrigData('frontend_input');

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } elseif ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } elseif ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Retrieve index type for indexable attribute
     *
     * @return string|false
     */
    public function getIndexType()
    {
        if (!$this->isIndexable()) {
            return false;
        }
        if ($this->getBackendType() == 'decimal') {
            return 'decimal';
        }

        return 'source';
    }

    /**
     * @codeCoverageIgnoreStart
     * {@inheritdoc}
     */
    public function getIsWysiwygEnabled()
    {
        return $this->getData(self::IS_WYSIWYG_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsHtmlAllowedOnFront()
    {
        return $this->getData(self::IS_HTML_ALLOWED_ON_FRONT);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsedForSortBy()
    {
        return $this->getData(self::USED_FOR_SORT_BY);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFilterable()
    {
        return $this->getData(self::IS_FILTERABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFilterableInSearch()
    {
        return $this->getData(self::IS_FILTERABLE_IN_SEARCH);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsUsedInGrid()
    {
        return (bool)$this->getData(self::IS_USED_IN_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisibleInGrid()
    {
        return (bool)$this->getData(self::IS_VISIBLE_IN_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFilterableInGrid()
    {
        return (bool)$this->getData(self::IS_FILTERABLE_IN_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSearchable()
    {
        return $this->getData(self::IS_SEARCHABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisibleInAdvancedSearch()
    {
        return $this->getData(self::IS_VISIBLE_IN_ADVANCED_SEARCH);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisibleOnFront()
    {
        return $this->getData(self::IS_VISIBLE_ON_FRONT);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsedInListing()
    {
        return $this->getData(self::USED_IN_LISTING);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsVisible()
    {
        return $this->getData(self::IS_VISIBLE);
    }

    public function getUsedInForm()
    {
        return $this->getData(self::USED_IN_FORM);
    }

    //@codeCoverageIgnoreEnd

    /**
     * {@inheritdoc}
     */
    public function getScope()
    {
        if ($this->isScopeGlobal()) {
            return self::SCOPE_GLOBAL_TEXT;
        } elseif ($this->isScopeWebsite()) {
            return self::SCOPE_WEBSITE_TEXT;
        } else {
            return self::SCOPE_STORE_TEXT;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltip()
    {
        return $this->getData(self::TOOLTIP);
    }

    /**
     * Set whether WYSIWYG is enabled flag
     *
     * @param bool $isWysiwygEnabled
     * @return $this
     */
    public function setIsWysiwygEnabled($isWysiwygEnabled)
    {
        return $this->setData(self::IS_WYSIWYG_ENABLED, $isWysiwygEnabled);
    }

    /**
     * Set whether the HTML tags are allowed on the frontend
     *
     * @param bool $isHtmlAllowedOnFront
     * @return $this
     */
    public function setIsHtmlAllowedOnFront($isHtmlAllowedOnFront)
    {
        return $this->setData(self::IS_HTML_ALLOWED_ON_FRONT, $isHtmlAllowedOnFront);
    }

    /**
     * Set whether it is used for sorting in product listing
     *
     * @param bool $usedForSortBy
     * @return $this
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        return $this->setData(self::USED_FOR_SORT_BY, $usedForSortBy);
    }

    /**
     * Set whether it used in layered navigation
     *
     * @param bool $isFilterable
     * @return $this
     */
    public function setIsFilterable($isFilterable)
    {
        return $this->setData(self::IS_FILTERABLE, $isFilterable);
    }

    /**
     * Set whether it is used in search results layered navigation
     *
     * @param bool $isFilterableInSearch
     * @return $this
     */
    public function setIsFilterableInSearch($isFilterableInSearch)
    {
        return $this->setData(self::IS_FILTERABLE_IN_SEARCH, $isFilterableInSearch);
    }

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Whether the attribute can be used in Quick Search
     *
     * @param string $isSearchable
     * @return $this
     */
    public function setIsSearchable($isSearchable)
    {
        return $this->setData(self::IS_SEARCHABLE, $isSearchable);
    }

    /**
     * Set whether the attribute can be used in Advanced Search
     *
     * @param string $isVisibleInAdvancedSearch
     * @return $this
     */
    public function setIsVisibleInAdvancedSearch($isVisibleInAdvancedSearch)
    {
        return $this->setData(self::IS_VISIBLE_IN_ADVANCED_SEARCH, $isVisibleInAdvancedSearch);
    }

    /**
     * Set whether the attribute is visible on the frontend
     *
     * @param string $isVisibleOnFront
     * @return $this
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        return $this->setData(self::IS_VISIBLE_ON_FRONT, $isVisibleOnFront);
    }

    /**
     * Set whether the attribute can be used in product listing
     *
     * @param string $usedInListing
     * @return $this
     */
    public function setUsedInListing($usedInListing)
    {
        return $this->setData(self::USED_IN_LISTING, $usedInListing);
    }

    public function setUsedInForm($usedInForm)
    {
        return $this->setData(self::USED_IN_FORM, $usedInForm);
    }

    /**
     * Set whether attribute is visible on frontend.
     *
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        return $this->setData(self::IS_VISIBLE, $isVisible);
    }

    /**
     * Set attribute scope
     *
     * @param string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        if ($scope == self::SCOPE_GLOBAL_TEXT) {
            return $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_GLOBAL);
        } elseif ($scope == self::SCOPE_WEBSITE_TEXT) {
            return $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_WEBSITE);
        } elseif ($scope == self::SCOPE_STORE_TEXT) {
            return $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_STORE);
        } else {
            //Ignore unrecognized scope
            return $this;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTooltip($tooltip)
    {
        return $this->setData(self::TOOLTIP, $tooltip);
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        $this->_eavConfig->clear();
        return parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function __sleep()
    {
        $this->unsetData('entity_type');
        return array_diff(
            parent::__sleep(),
            ['_indexerEavProcessor', '_vendorFlatIndexerProcessor', '_vendorFlatIndexerHelper', 'attrLockValidator']
        );
    }

    /**
     * @inheritdoc
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_indexerEavProcessor = $objectManager->get(\Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Processor::class);
        $this->_vendorFlatIndexerProcessor = $objectManager->get(
            \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Processor::class
        );
        $this->_vendorFlatIndexerHelper = $objectManager->get(\Omnyfy\Vendor\Helper\Vendor\Flat\Indexer::class);
        $this->attrLockValidator = $objectManager->get(LockValidatorInterface::class);
    }
}