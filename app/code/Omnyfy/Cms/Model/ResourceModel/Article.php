<?php

namespace Omnyfy\Cms\Model\ResourceModel;

/**
 * Cms category resource model
 */
class Article extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
    \Magento\Framework\Model\ResourceModel\Db\Context $context, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\Stdlib\DateTime $dateTime, $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('omnyfy_cms_article', 'article_id');
    }

    /**
     * Process article data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(
    \Magento\Framework\Model\AbstractModel $object
    ) {
        $condition = ['article_id = ?' => (int) $object->getId()];
        $tableSufixs = [
            'store',
            'category',
            'tag',
            'user_type',
            'relatedproduct',
            'relatedarticle',
            'relatedarticle',
        ];
        foreach ($tableSufixs as $sufix) {
            $this->getConnection()->delete(
                    $this->getTable('omnyfy_cms_article_' . $sufix), ($sufix == 'relatedarticle') ? ['related_id = ?' => (int) $object->getId()] : $condition
            );
        }

        return parent::_beforeDelete($object);
    }

    /**
     * Process article data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) {
        foreach (['publish_time', 'custom_theme_from', 'custom_theme_to'] as $field) {
            $value = $object->getData($field) ? : null;
            $object->setData($field, $this->dateTime->formatDate($value));
        }

        $identifierGenerator = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Omnyfy\Cms\Model\ResourceModel\PageIdentifierGenerator');
        $identifierGenerator->generate($object);

        if (!$this->isValidPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __('The article URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->isNumericPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __('The article URL key cannot be made of only numbers.')
            );
        }

        $gmtDate = $this->_date->gmtDate();

        if ($object->isObjectNew() && !$object->getCreationTime()) {
            $object->setCreationTime($gmtDate);
        }

        if (!$object->getPublishTime()) {
            $object->setPublishTime($object->getCreationTime());
        }

        $object->setUpdateTime($gmtDate);

        return parent::_beforeSave($object);
    }

    /**
     * Assign article to store views, categories, related articles, etc.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) {
        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array) $object->getStoreIds();
        if (!$newIds) {
            $newIds = [0];
        }
        $counterUpdate = $object->getData('article_counter_update') ? 0 : 1;
        $this->_updateLinks($object, $newIds, $oldIds, 'omnyfy_cms_article_store', 'store_id');

        /* Save category & tag links */
        if ($counterUpdate) {
            foreach (['category' => 'categories', 'tag' => 'tags', 'user_type' => 'user_types', 'service_category' => 'service_category'] as $linkType => $dataKey) {
                $newIds = (array) $object->getData($dataKey);
                foreach ($newIds as $key => $id) {
                    if (!$id) { // e.g.: zero
                        unset($newIds[$key]);
                    }
                }
                if (is_array($newIds)) {
                    if ($linkType == 'service_category') {
                        $lookup = 'lookupServiceCategoryIds';
                        $field = 'catelog_category_id';
                    } else if ($linkType == 'user_type') {
                        $lookup = 'lookupUserTypeIds';
                        $field = 'user_type_id';
                    } else {
                        $lookup = 'lookup' . ucfirst($linkType) . 'Ids';
                        $field = $linkType . '_id';
                    }
                    $oldIds = $this->$lookup($object->getId());
                    $this->_updateLinks(
                            $object, $newIds, $oldIds, 'omnyfy_cms_article_' . $linkType, $field
                    );
                }
            }
        }

        /* Save tags links */
        $newIds = (array) $object->getTags();
        foreach ($newIds as $key => $id) {
            if (!$id) { // e.g.: zero
                unset($newIds[$key]);
            }
        }
        if (is_array($newIds)) {
            $oldIds = $this->lookupTagIds($object->getId());
            $this->_updateLinks($object, $newIds, $oldIds, 'omnyfy_cms_article_tag', 'tag_id');
        }

        /* Save userTypes links */
        $newIds1 = (array) $object->getUserTypes();
        foreach ($newIds1 as $key => $id) {
            if (!$id) { // e.g.: zero
                unset($newIds1[$key]);
            }
        }
        if (is_array($newIds1)) {
            $oldIds1 = $this->lookupUserTypeIds($object->getId());
            $this->_updateLinks($object, $newIds1, $oldIds1, 'omnyfy_cms_article_user_type', 'user_type_id');
        }

        /* Save Service Category links */
        $newIds2 = (array) $object->getServiceCategory();

        if ($counterUpdate) {
            foreach ($newIds2 as $key => $id) {
                if (!$id) { // e.g.: zero
                    unset($newIds2[$key]);
                }
            }
            if (is_array($newIds2)) {
                $oldIds2 = $this->lookupServiceCategoryIds($object->getId());
                $this->_updateLinks($object, $newIds2, $oldIds2, 'omnyfy_cms_article_service_category', 'catelog_category_id');
            }
        }

        /* Save related article, product & service vendors links */
        if ($counterUpdate) {
            //if ($links = $object->getData('links')) {
            $links = $object->getData('links');
            //if (is_array($links)) {
            foreach (['article' => 'related_id', 'product' => 'related_id', 'service' => 'vendor_id', 'tool' => 'tool_template_id'] as $linkType => $linkField) {
                $tableName = 'omnyfy_cms_article_related' . $linkType;
                if ($linkType == 'service') {
                    $tableName = 'omnyfy_cms_article_vendor';
                } else if ($linkType == 'tool') {
                    $tableName = 'omnyfy_cms_article_tool_template';
                }
                if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = $links[$linkType];
                    $linksDataKeys = array_keys($linksData);
                } else {
                    $linksData = [];
                    $linksDataKeys = [];
                }
                $lookup = 'lookupRelated' . ucfirst($linkType) . 'Ids';
                $oldIds = $this->$lookup($object->getId());

                $this->_updateLinks(
                        $object, $linksDataKeys, $oldIds, $tableName, $linkField, $linksData
                );
                //} 
            }
            //}
            //}
        }

        return parent::_afterSave($object);
    }

    /**
     * Update article connections
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  Array $newRelatedIds
     * @param  Array $oldRelatedIds
     * @param  String $tableName
     * @param  String  $field
     * @param  Array  $rowData
     * @return void
     */
    protected function _updateLinks(
    \Magento\Framework\Model\AbstractModel $object, Array $newRelatedIds, Array $oldRelatedIds, $tableName, $field, $rowData = []
    ) {
        $table = $this->getTable($tableName);

        $insert = $newRelatedIds;
        $delete = $oldRelatedIds;

        if (!empty($delete)) {
            $where = ['article_id = ?' => (int) $object->getId(), $field . ' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if (!empty($insert)) {
            $data = [];

            foreach ($insert as $id) {
                $id = (int) $id;
                $data[] = array_merge(['article_id' => (int) $object->getId(), $field => $id], (isset($rowData[$id]) && is_array($rowData[$id])) ? $rowData[$id] : []
                );
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null) {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object) {
        if ($object->getId()) {
            $storeIds = $this->lookupStoreIds($object->getId());
            $object->setData('store_ids', $storeIds);

            $categories = $this->lookupCategoryIds($object->getId());
            $object->setCategories($categories);

            $tags = $this->lookupTagIds($object->getId());
            $object->setTags($tags);

            $userTypes = $this->lookupUserTypeIds($object->getId());
            $object->setUserTypes($userTypes);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Check if article identifier exist for specific store
     * return article id if article exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    protected function _getLoadByIdentifierSelect($identifier, $storeIds, $isActive = null) {
        $select = $this->getConnection()->select()->from(
                        ['cp' => $this->getMainTable()]
                )->join(
                        ['cps' => $this->getTable('omnyfy_cms_article_store')], 'cp.article_id = cps.article_id', []
                )->where(
                        'cp.identifier = ?', $identifier
                )->where(
                'cps.store_id IN (?)', $storeIds
        );

        if (!is_null($isActive)) {
            $select->where('cp.is_active = ?', $isActive)
                    ->where('cp.publish_time <= ?', $this->_date->gmtDate());
        }
        return $select;
    }

    /**
     *  Check whether article identifier is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(\Magento\Framework\Model\AbstractModel $object) {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether article identifier is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(\Magento\Framework\Model\AbstractModel $object) {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * Check if article identifier exist for specific store
     * return article id if article exists
     *
     * @param string $identifier
     * @param int|array $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeIds) {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $storeIds[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $select = $this->_getLoadByIdentifierSelect($identifier, $storeIds, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('cp.article_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupStoreIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_store', 'store_id');
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupCategoryIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_category', 'category_id');
    }

    /**
     * Get tag ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupTagIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_tag', 'tag_id');
    }

    /**
     * Get user_type ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupUserTypeIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_user_type', 'user_type_id');
    }

    /**
     * Get user_type ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupServiceCategoryIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_service_category', 'catelog_category_id');
    }

    /**
     * Get related article ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupRelatedArticleIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_relatedarticle', 'related_id');
    }

    /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupRelatedProductIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_relatedproduct', 'related_id');
    }

    /**
     * Get related vendor location ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupRelatedServiceIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_vendor', 'vendor_id');
    }

    /**
     * Get related tool ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupRelatedToolIds($articleId) {
        return $this->_lookupIds($articleId, 'omnyfy_cms_article_tool_template', 'tool_template_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $articleId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($articleId, $tableName, $field) {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
                        $this->getTable($tableName), $field
                )->where(
                'article_id = ?', (int) $articleId
        );

        return $adapter->fetchCol($select);
    }

}
