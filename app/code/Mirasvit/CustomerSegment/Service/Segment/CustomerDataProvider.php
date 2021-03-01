<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Service\Segment;


use Magento\Framework\App\ResourceConnection;
use Mirasvit\CustomerSegment\Api\Service\Segment\CustomerDataProviderInterface;
use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer\Collection as SegmentCustomerCollection;

class CustomerDataProvider implements CustomerDataProviderInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * CustomerSelectService constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function provideCustomerInfo(SegmentCustomerCollection $collection)
    {
        return $collection->getSelect()
            ->columns([
                'type'           => 'IF(customer.entity_id IS NOT NULL, "Customer", "Guest")',
                'customer_email' => 'main_table.email'
            ])
            ->joinLeft(['segment' => $this->resourceConnection->getTableName('mst_customersegment_segment')],
                'main_table.segment_id = segment.segment_id',
                []
            )
            ->joinLeft(['order' => $this->resourceConnection->getTableName('sales_order')],
                'main_table.email = order.customer_email',
                []
            )
            ->joinLeft(['customer' => $this->resourceConnection->getTableName('customer_entity')],
                'main_table.email = customer.email AND customer.website_id = segment.website_id',
                [
                    'name' => 'CONCAT(IFNULL(customer.firstname, order.customer_firstname), " ", IFNULL(customer.lastname, order.customer_lastname))',
                    'group_id' => 'customer.group_id',
                ]
            )
            ->joinLeft(['billing' => $this->resourceConnection->getTableName('customer_address_entity')],
                'customer.default_billing = billing.entity_id',
                [
                    'telephone' => 'IFNULL(billing.telephone, sales_billing.telephone)'
                ]
            )
            ->joinLeft(['sales_billing' => $this->resourceConnection->getTableName('sales_order_address')],
                'main_table.billing_address_id = sales_billing.entity_id',
                []
            )
            ->group('main_table.email');
    }

    /**
     * @inheritDoc
     */
    public function countUniqueCustomers()
    {
        $adapter = $this->resourceConnection->getConnection();
        $select = $adapter->select()
            ->from($this->resourceConnection->getTableName('mst_customersegment_segment_customer'), [
                new \Zend_Db_Expr('COUNT(DISTINCT email)')
            ]);

        return $adapter->fetchOne($select);
    }
}
