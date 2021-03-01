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



namespace Mirasvit\CustomerSegment\Api\Service\Segment;


use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DB\Adapter\AdapterInterface;

interface AttributeServiceInterface
{
    /**
     * Fetch validated attribute value from model.
     *
     * @param AdapterInterface $adapter
     * @param AbstractModel    $model
     *
     * @return mixed
     */
    public function getAttributeValue(AdapterInterface $adapter, AbstractModel $model);
}