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



namespace Mirasvit\CustomerSegment\Reports;

use Mirasvit\Report\Model\AbstractReport;

/**
 * This report displays only segment customers with orders.
 */
class Sales extends AbstractReport
{
    const ID = '';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return __('Sales by Segment');
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return self::ID;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->setTable('mst_customersegment_segment');

        $this->setColumns([
            'mst_customersegment_segment|segment_id',
            'sales_order|total_qty_ordered__sum',
            'sales_order|discount_amount__sum',
            'sales_order|shipping_amount__sum',
            'sales_order|tax_amount__sum',
            'sales_order|total_refunded__sum',
            'sales_order|subtotal__sum',
            'sales_order|grand_total__sum',
        ]);

        $this->setDimensions([
            'mst_customersegment_segment|segment_id',
        ]);

        $this->setPrimaryFilters(['mst_customersegment_segment|segment_id']);


        $this->addFastFilters([
            'sales_order|created_at',
            'mst_customersegment_segment|segment_id',
        ]);

        $this->getChartConfig()
            ->setType('column')
            ->setDefaultColumns([
                'sales_order|grand_total__sum',
            ]);
    }
}
