<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model\Value;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\Orderattr\Model\Entity\EntityResolver;

class LastCheckoutValue
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var bool|null|\Amasty\Orderattr\Model\Entity\EntityData
     */
    private $entity;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    private $orderCollection;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EntityResolver
     */
    private $entityResolver;

    public function __construct(
        EntityResolver $entityResolver,
        CustomerSession\Proxy $customerSession,
        CollectionFactory $orderCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->orderCollection = $orderCollectionFactory->create();
        $this->storeManager = $storeManager;
        $this->entityResolver = $entityResolver;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeInterface
     *
     * @return bool|mixed
     */
    public function retrieve($attribute)
    {
        if ($this->entity === null) {
            if ($customerId = $this->customerSession->getId()) {
                $lastOrder = $this->orderCollection->addFieldToSelect('entity_id')
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('store_id', $this->storeManager->getStore()->getId())
                    ->setOrder('entity_id')
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getItems();
                if (!empty($lastOrder)) {
                    $this->entity = $this->entityResolver->getEntityByOrder(end($lastOrder));
                }
            }
            if ($this->entity === null) {
                $this->entity = false;
            }
        }

        if ($this->entity && $value = $this->entity->getCustomAttribute($attribute->getAttributeCode())) {

            return $value->getValue();
        }

        return false;
    }
}