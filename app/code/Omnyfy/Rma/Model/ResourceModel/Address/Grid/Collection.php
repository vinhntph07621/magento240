<?php
/**
 * Project:
 * Author: seth
 * Date: 21/2/20
 * Time: 5:11 pm
 **/

namespace Omnyfy\Rma\Model\ResourceModel\Address\Grid;


use Magento\Framework\Api\Search\AggregationInterface;
use Mirasvit\Rma\Api\Data\AddressInterface;

class Collection extends \Mirasvit\Rma\Model\ResourceModel\Address\Grid\Collection
{
    /**
     * Add filter by vendor id.
     */
    protected function _initSelect()
    {
        /**
         * _construct doesn't work - have to use Object manager!
         */
        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var $helper \Omnyfy\Rma\Helper\Data */
        $helper = $ObjectManager->get('Omnyfy\Rma\Helper\Data');

        $select = $this->getSelect();
        if ($vendorId = $helper->getVendorId()) {
            $select->where("main_table.vendor_id = $vendorId");
        }
        parent::_initSelect();
    }

}