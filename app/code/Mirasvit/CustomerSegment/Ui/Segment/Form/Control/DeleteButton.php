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

use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
class DeleteButton extends ButtonAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [SegmentInterface::ID => $this->getId()]);
    }
}
