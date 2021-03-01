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



namespace Mirasvit\Rma\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mirasvit\Rma\Api\Data\ItemInterface;
use Mirasvit\Rma\Repository\ItemRepository;
use Mirasvit\Rma\Repository\OfflineItemRepository;

class Condition extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    private $itemRepository;
    private $offlineItemRepository;

    /**
     * Condition constructor.
     * @param ItemRepository $itemRepository
     * @param OfflineItemRepository $offlineItemRepository
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        ItemRepository $itemRepository,
        OfflineItemRepository $offlineItemRepository,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->itemRepository        = $itemRepository;
        $this->offlineItemRepository = $offlineItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('mst_rma_condition', 'condition_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rma\Model\Condition $object */
        if (!$object->getIsMassDelete()) {
        }

        return parent::_afterLoad($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rma\Model\Condition $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        return parent::_beforeSave($object);
    }

    /**
     * @param  \Mirasvit\Rma\Model\Condition $object
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getIsMassStatus()) {
        }

        return parent::_afterSave($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->isConditionUsed($object)) {
            throw new LocalizedException(
                __('Condition "%1" is used in the existing RMA(s). Please remove the condition from RMA(s)', $object->getName())
            );
        }
        return parent::_beforeDelete($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $condition
     *
     * @return bool
     */
    private function isConditionUsed($condition)
    {
        $collection = $this->itemRepository->getCollection()
            ->addFieldToFilter('main_table.'.ItemInterface::KEY_CONDITION_ID, $condition->getId())
        ;
        $offlineCollection = $this->offlineItemRepository->getCollection()
            ->addFieldToFilter('main_table.'.ItemInterface::KEY_CONDITION_ID, $condition->getId())
        ;

        return $collection->count() > 0 || $offlineCollection->count() > 0;
    }
}
