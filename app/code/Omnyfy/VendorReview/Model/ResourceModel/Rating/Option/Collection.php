<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Model\ResourceModel\Rating\Option;

/**
 * Rating option collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Rating votes table
     *
     * @var string
     */
    protected $_ratingVoteTable;

    /**
     * Define model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorReview\Model\Rating\Option', 'Omnyfy\VendorReview\Model\ResourceModel\Rating\Option');
        $this->_ratingVoteTable = $this->getTable('vendor_rating_option_vote');
    }

    /**
     * Add rating filter
     *
     * @param   int|array $rating
     * @return  $this
     */
    public function addRatingFilter($rating)
    {
        if (is_numeric($rating)) {
            $this->addFilter('vendor_rating_id', $rating);
        } elseif (is_array($rating)) {
            $this->addFilter('vendor_rating_id', $this->_getConditionSql('vendor_rating_id', ['in' => $rating]), 'string');
        }
        return $this;
    }

    /**
     * Set order by position field
     *
     * @param   string $dir
     * @return  $this
     */
    public function setPositionOrder($dir = 'ASC')
    {
        $this->setOrder('main_table.position', $dir);
        return $this;
    }
}
