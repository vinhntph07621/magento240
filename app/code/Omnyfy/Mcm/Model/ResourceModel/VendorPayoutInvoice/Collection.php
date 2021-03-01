<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorPayoutInvoice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init(
            'Omnyfy\Mcm\Model\VendorPayoutInvoice', 'Omnyfy\Mcm\Model\ResourceModel\VendorPayoutInvoice'
        );
        $this->addFilterToMap('vendor_id', 'main_table.vendor_id');
    }

    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
        $this->getSelect()
            ->joinLeft(
                ['ve' => $this->getTable('omnyfy_vendor_vendor_entity')], 'main_table.vendor_id = ve.entity_id', [
                    'vendor_name' => 've.name',
                    'vendor_status' => 've.status'
                ]
            );

        if ($entityTypeId = $this->getEntityTypeId())
        {
            if ($attributeId = $this->getAttributeId('address',$entityTypeId)){
                $this->getSelect()
                    ->joinLeft(
                        [ 'address' => $this->getTable('omnyfy_vendor_vendor_entity_varchar')],
                        'main_table.vendor_id = address.entity_id AND address.attribute_id='.$attributeId,
                        [
                            'vendor_address' => 'address.value'
                        ]
                    );
            }

            if ($attributeId = $this->getAttributeId('phone',$entityTypeId)){
                $this->getSelect()
                    ->joinLeft(
                        [ 'phone' => $this->getTable('omnyfy_vendor_vendor_entity_varchar')],
                        'main_table.vendor_id = phone.entity_id AND phone.attribute_id='.$attributeId,
                        [
                            'vendor_phone' => 'phone.value'
                        ]
                    );
            }

            if ($attributeId = $this->getAttributeId('abn',$entityTypeId)){
                $this->getSelect()
                    ->joinLeft(
                        [ 'abn' => $this->getTable('omnyfy_vendor_vendor_entity_varchar')],
                        'main_table.vendor_id = abn.entity_id AND abn.attribute_id='.$attributeId,
                        [
                            'vendor_abn' => 'abn.value'
                        ]
                    );
            }
        }

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo)) {
            $this->getSelect()->where('vendor_id=' . $vendorInfo['vendor_id']);
        }
    }

    protected function getEntityTypeId(){
        try {
            $conn = $this->getConnection();
            $table = $conn->getTableName('eav_entity_type');
            $select = $conn->select()
                ->from($table, ['entity_type_id'])
                ->where('entity_type_code=?', \Omnyfy\Vendor\Model\Vendor::ENTITY);
            return $conn->fetchOne($select);
        }catch(\Exception $exception){
            return null;
        }
    }

    protected function getAttributeId($attributeCode, $entityTypeId){
        try {
            $conn = $this->getConnection();
            $table = $conn->getTableName('eav_attribute');
            $select = $conn->select()
                ->from($table, ['attribute_id'])
                ->where('attribute_code =?', $attributeCode)
                ->where('entity_type_id =?',$entityTypeId);

            $output = $conn->fetchOne($select);

            return $output;
        } catch(\Exception $exception){
            return null;
        }
    }

}
