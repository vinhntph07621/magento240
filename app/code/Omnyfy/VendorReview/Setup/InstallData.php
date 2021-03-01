<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Omnyfy\VendorReview\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        
        //Fill table review/omnyfy_vendor_reviewentity
        $reviewEntityCodes = [
            \Omnyfy\VendorReview\Model\Review::ENTITY_PRODUCT_CODE,
            \Omnyfy\VendorReview\Model\Review::ENTITY_CUSTOMER_CODE,
            \Omnyfy\VendorReview\Model\Review::ENTITY_CATEGORY_CODE,
        ];
        foreach ($reviewEntityCodes as $entityCode) {
            $installer->getConnection()->insert($installer->getTable('omnyfy_vendor_review_entity'), ['entity_code' => $entityCode]);
        }
        
        //Fill table review/omnyfy_vendor_reviewentity
        $reviewStatuses = [
            \Omnyfy\VendorReview\Model\Review::STATUS_APPROVED => 'Approved',
            \Omnyfy\VendorReview\Model\Review::STATUS_PENDING => 'Pending',
            \Omnyfy\VendorReview\Model\Review::STATUS_NOT_APPROVED => 'Not Approved',
        ];
        foreach ($reviewStatuses as $k => $v) {
            $bind = ['status_id' => $k, 'status_code' => $v];
            $installer->getConnection()->insertForce($installer->getTable('omnyfy_vendor_review_status'), $bind);
        }
        
        $data = [
            \Omnyfy\VendorReview\Model\Rating::ENTITY_PRODUCT_CODE => [
                ['vendor_rating_code' => 'Quality', 'position' => 0],
                ['vendor_rating_code' => 'Value', 'position' => 0],
                ['vendor_rating_code' => 'Price', 'position' => 0],
            ],
            \Omnyfy\VendorReview\Model\Rating::ENTITY_PRODUCT_REVIEW_CODE => [],
            \Omnyfy\VendorReview\Model\Rating::ENTITY_REVIEW_CODE => [],
        ];
        
        foreach ($data as $entityCode => $ratings) {
            //Fill table rating/vendor_rating_entity
            $installer->getConnection()->insert($installer->getTable('vendor_rating_entity'), ['entity_code' => $entityCode]);
            $entityId = $installer->getConnection()->lastInsertId($installer->getTable('vendor_rating_entity'));
        
            foreach ($ratings as $bind) {
                //Fill table rating/rating
                $bind['entity_id'] = $entityId;
                $installer->getConnection()->insert($installer->getTable('vendor_rating'), $bind);
        
                //Fill table rating/vendor_rating_option
                $ratingId = $installer->getConnection()->lastInsertId($installer->getTable('vendor_rating'));
                $optionData = [];
                for ($i = 1; $i <= 5; $i++) {
                    $optionData[] = ['vendor_rating_id' => $ratingId, 'code' => (string)$i, 'value' => $i, 'position' => $i];
                }
                $installer->getConnection()->insertMultiple($installer->getTable('vendor_rating_option'), $optionData);
            }
        }
        
    }
}
