<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel\Tag;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\Faq\Setup\Operation\CreateQuestionStoreTable;

/**
 * @method \Amasty\Faq\Model\Tag[] getItems()
 */
class Collection extends AbstractCollection
{
    const CACHE_TAG = 'amfaq_tags';

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Faq\Model\Tag::class, \Amasty\Faq\Model\ResourceModel\Tag::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @param int $limit
     * @param int|null $storeId
     *
     * @return \Amasty\Faq\Model\Tag[]
     */
    public function getTagsSortedByCount($limit = 0, $storeId = null)
    {
        $this->getSelect()
            ->joinLeft(
                ['question_tag' => $this->getTable(\Amasty\Faq\Setup\Operation\CreateQuestionTagTable::TABLE_NAME)],
                'question_tag.tag_id = main_table.tag_id',
                ['count' => 'COUNT(question_tag.tag_id)']
            )->group('main_table.title')
            ->order('count DESC');
        if ($limit) {
            $this->getSelect()->limit($limit);
        }
        if (null !== $storeId) {
            $productSelects = $this->getConnection()
                ->select()
                ->from(
                    $this->getTable(CreateQuestionStoreTable::TABLE_NAME),
                    'question_id'
                )->where('store_id = 0')
                ->orWhere('store_id = ?', $storeId);
            $productIds = $this->getConnection()->fetchCol($productSelects);
            $this->getSelect()->where('question_tag.question_id IN (?)', $productIds);
        }

        return $this->getItems();
    }
}
