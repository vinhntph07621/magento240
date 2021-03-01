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



namespace Mirasvit\CustomerSegment\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;

class Menu extends AbstractMenu
{
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);

        $this->visibleAt([
            'customersegment',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'id'       => 'segment',
            'resource' => 'Mirasvit_CustomerSegment::customersegment_segment',
            'title'    => __('Segments'),
            'url'      => $this->urlBuilder->getUrl('customersegment/segment'),
        ])->addItem([
            'resource' => 'Mirasvit_CustomerSegment::customersegment_report',
            'title'    => __('Reports'),
            'url'      => $this->urlBuilder->getUrl('customersegment/report'),
        ]);

        $this->addItem([
            'resource' => 'Mirasvit_CustomerSegment::customersegment_segment',
            'title'    => __('Add New'),
            'url'      => $this->urlBuilder->getUrl('customersegment/segment/new'),
        ], 'segment');

        $this->addSeparator();
        $this->addItem([
            'resource' => 'Mirasvit_CustomerSegment::customersegment_settings',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/customersegment'),
        ]);

        return $this;
    }
}
