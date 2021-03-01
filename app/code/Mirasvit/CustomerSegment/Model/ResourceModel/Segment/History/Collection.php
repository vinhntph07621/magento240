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



namespace Mirasvit\CustomerSegment\Model\ResourceModel\Segment\History;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = HistoryInterface::ID;

    protected function _construct()
    {
        $this->_init(
            'Mirasvit\CustomerSegment\Model\Segment\History',
            'Mirasvit\CustomerSegment\Model\ResourceModel\Segment\History'
        );
    }
}