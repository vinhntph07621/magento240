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
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Mirasvit\Rma\Api\Data\ItemInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method \Mirasvit\Rma\Model\ResourceModel\Item\Collection|\Mirasvit\Rma\Model\Item[] getCollection()
 * @method \Mirasvit\Rma\Model\Item load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rma\Model\Item setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rma\Model\Item setIsMassStatus(bool $flag)
 * @method \Mirasvit\Rma\Model\ResourceModel\Item getResource()
 */
class OfflineItem extends \Magento\Framework\Model\AbstractModel implements \Mirasvit\Rma\Api\Data\OfflineItemInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_offline_item';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rma\Model\ResourceModel\OfflineItem');
    }

    /**
     * {@inheritdoc}
     */
    public function getIsOffline()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineItemId()
    {
        return $this->getData(self::KEY_OFFLINE_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOfflineItemId($offlineItemId)
    {
        return $this->setData(self::KEY_OFFLINE_ITEM_ID, $offlineItemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaId()
    {
        return $this->getData(self::KEY_RMA_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRmaId($rmaId)
    {
        return $this->setData(self::KEY_RMA_ID, $rmaId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineOrderId()
    {
        return $this->getData(self::KEY_OFFLINE_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOfflineOrderId($orderId)
    {
        return $this->setData(self::KEY_OFFLINE_ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonId()
    {
        return $this->getData(self::KEY_REASON_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setReasonId($reasonId)
    {
        return $this->setData(self::KEY_REASON_ID, $reasonId);
    }

    /**
     * {@inheritdoc}
     */
    public function getResolutionId()
    {
        return $this->getData(self::KEY_RESOLUTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setResolutionId($resolutionId)
    {
        return $this->setData(self::KEY_RESOLUTION_ID, $resolutionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionId()
    {
        return $this->getData(self::KEY_CONDITION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionId($conditionId)
    {
        return $this->setData(self::KEY_CONDITION_ID, $conditionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyRequested()
    {
        return $this->getData(self::KEY_QTY_REQUESTED);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyRequested($qtyRequested)
    {
        return $this->setData(self::KEY_QTY_REQUESTED, $qtyRequested);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::KEY_CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::KEY_CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::KEY_UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::KEY_UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        $this->_data['is_offline'] = true;

        return parent::getData($key, $index);
    }

}
