<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-15
 * Time: 16:20
 */
namespace Omnyfy\VendorSubscription\Model\Resource\Usage;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Usage', 'Omnyfy\VendorSubscription\Model\Resource\Usage');
    }

    public function addVendorFilter($vendorId)
    {
        if (!$this->getFlag('has_vendor_filter')) {
            $this->addFieldToFilter('vendor_id', $vendorId);
            $this->setFlag('has_vendor_filter', 1);
        }

        return $this;
    }

    public function addNowFilter()
    {
        if (!$this->getFlag('has_now_filter')) {
            $now = new \Zend_Db_Expr('NOW()');
            $this->addFieldToFilter('start_date', ['to' => $now])
                ->addFieldToFilter(
                    [
                        'end_date',
                        'end_date'
                    ],
                    [
                        ['from' => $now],
                        ['null' => true]
                    ]
                )
            ;

            $this->setFlag('has_now_filter', 1);
        }

        return $this;
    }

    public function addOneOffFilter($isOneOff)
    {
        if (!$this->getFlag('has_one_off_filter')) {
            $this->addFieldToFilter('is_one_off', intval($isOneOff));

            $this->setFlag('has_one_off_filter', 1);
        }

        return $this;
    }

    public function addUsageRemainField()
    {
        if (!$this->getFlag('has_usage_remain_field')) {
            $this->addExpressionFieldToSelect(
                'usage_remain',
                '({{0}} - {{1}})',
                [
                    'main_table.usage_limit',
                    'main_table.usage_count'
                ]
            );

            $this->setFlag('has_usage_remain_field', 1);
        }

        return $this;
    }

    public function addRemainOrder($mostAvailableFirst)
    {
        $this->addUsageRemainField();

        if ($mostAvailableFirst) {
            $this->addOrder('usage_remain', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
        }
        else{
            $this->addOrder('usage_remain', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        }

        return $this;
    }
}
 