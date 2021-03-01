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



namespace Mirasvit\CustomerSegment\Ui\Segment\Form\Control;

class RefreshButton extends ButtonAbstract
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $segmentId = $this->getId();

        if ($segmentId) {
            return [
                'label'      => __('Refresh Segment Data'),
                'class'      => 'action',
                'sort_order' => 40,
                'on_click'   => "require('uiRegistry').get('customersegment_segment_form.customersegment_segment_form.segment.refresh').refresh()",
            ];
        }

        return [];
    }
}
