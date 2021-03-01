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
 * @package   mirasvit/module-reports
 * @version   1.3.39
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Reports\Reports\Catalog;

use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Context;
use Mirasvit\Reports\Model\ConfigProvider;

class Attribute extends AbstractReport
{
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        Context $context
    ) {
        $this->configProvider = $configProvider;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Sales by Attribute');
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setTable('catalog_product_entity');

        $this->setPrimaryFilters([
            'sales_order|created_at',
            'sales_order|store_id',
            'catalog_product_entity|type_id',
            'sales_order|status',
        ]);

        $this->setInternalColumns(['catalog_product_entity|entity_id']);

        $this->setColumns([
            'sales_order_item|qty_ordered__sum',
            'sales_order_item|tax_amount__sum',
            'sales_order_item|discount_amount__sum',
            'sales_order_item|amount_refunded__sum',
            'sales_order_item|row_total__sum',
        ]);

        $this->setDimensions([
            'catalog_product_entity|status',
        ]);

        $this->setPrimaryDimensions($this->context->getProvider()->getSimpleColumns('catalog_product_entity'));

        $this->getGridConfig()
            ->disablePagination();

        $this->getChartConfig()
            ->setDefaultColumns([
                'sales_order_item|row_total__sum',
            ])
            ->setType('pie');

        $this->setFilters([
            [
                'column'        => 'sales_order|status',
                'conditionType' => 'in',
                'value'         => $this->configProvider->getFilterableOrderStatuses(),
            ],
        ]);
    }
}
