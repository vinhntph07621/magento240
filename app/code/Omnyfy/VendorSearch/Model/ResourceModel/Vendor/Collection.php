<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 18/07/2019
 * Time: 11:35 AM
 */

namespace Omnyfy\VendorSearch\Model\ResourceModel\Vendor;


class Collection extends \Omnyfy\Vendor\Model\Resource\Vendor\Collection
{
    protected function _getItemId(\Magento\Framework\DataObject $item) {
        return $item->getId() . '_' . $item->getData('location_id');
    }

    public function joinLocation($postcode = null){
        $this->getSelect()->join(
            ['vl'=>$this->getTable('omnyfy_vendor_location_entity')],
            'vl.vendor_id = e.entity_id',
            [
                'location_id' => 'vl.entity_id',
                'location_name' => 'vl.location_name',
                'location_address' => 'vl.address',
                'location_suburb' => 'vl.suburb',
                'location_postcode' => 'vl.postcode',
                'location_region' => 'vl.region',
                'location_country' => 'vl.country',
            ]
        );

        $this->getSelect()->where(
            'vl.status=1'
        );

        if ($postcode){
            $this->getSelect()->where(
                'vl.postcode='.$postcode
            );
        }

        return $this;
    }

    public function setNumSuburbs(){
        /*$this->getSelect()->columns(['suburb_count' => new \Zend_Db_Expr('COUNT(vl.entity_id)-1')])
            ->group(array('vl.vendor_id','vl.suburb'));*/

        return $this;
    }

    public function getLocationCount(){
        $this->getSelect()->columns(['location_count' => new \Zend_Db_Expr('COUNT(vl.entity_id)-1')])
            ->group('vl.vendor_id');

        return $this;
    }
}