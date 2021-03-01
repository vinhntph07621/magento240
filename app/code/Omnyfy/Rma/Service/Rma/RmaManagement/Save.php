<?php
/**
 * Project:
 * Author: seth
 * Date: 21/2/20
 * Time: 10:49 am
 **/

namespace Omnyfy\Rma\Service\Rma\RmaManagement;

use Omnyfy\Vendor\Model\Resource\Vendor as VendorResource;

class Save extends \Mirasvit\Rma\Service\Rma\RmaManagement\Save
{
    protected $vendorResource;

    protected $messageRepository;

    protected $rmaSearchManagement;

    protected $itemUpdateService;

    protected $messageAddService;

    protected $rmaManagement;

    protected $rmaOrderService;

    protected $registry;

    protected $rmaFactory;

    protected $request;

    protected $eventManager;

    protected $orderAbstractFactory;

    public function __construct(
        \Mirasvit\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SearchInterface $rmaSearchManagement,
        \Mirasvit\Rma\Api\Service\Message\MessageManagement\AddInterface $messageAddService,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaOrderInterface $rmaOrderService,
        \Mirasvit\Rma\Model\RmaFactory $rmaFactory,
        \Mirasvit\Rma\Service\Item\Update $itemUpdateService,
        \Mirasvit\Rma\Service\Order\OrderAbstractFactory $orderAbstractFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $registry,
        VendorResource $vendorResource
    ) {
        $this->messageRepository      = $messageRepository;
        $this->rmaSearchManagement    = $rmaSearchManagement;
        $this->itemUpdateService      = $itemUpdateService;
        $this->messageAddService      = $messageAddService;
        $this->rmaManagement          = $rmaManagement;
        $this->rmaOrderService        = $rmaOrderService;
        $this->registry               = $registry;
        $this->rmaFactory             = $rmaFactory;
        $this->request                = $request;
        $this->eventManager           = $eventManager;
        $this->orderAbstractFactory   = $orderAbstractFactory;
        $this->vendorResource         = $vendorResource;
        parent::__construct($messageRepository,
            $rmaSearchManagement,
            $messageAddService,
            $rmaManagement,
            $rmaOrderService,
            $rmaFactory,
            $itemUpdateService,
            $orderAbstractFactory,
            $request,
            $eventManager,
            $registry
        );
    }

    /**
     * {@inheritdoc}
     */
    public function saveRma($performer, $data, $items)
    {
        $index = 0;
        $orderedItems = [];

        /**
         * Group Ordered items per vendor first.
         */
        foreach ($items as $orderItemId => $value) {
            $vendorId = $value['vendor_id'];
            $orderedItems[$vendorId][] = $value;
        }

        $vendors = [];
        foreach ($orderedItems as $orderedItem) {
            foreach ($orderedItem as $key => $value) {

                if (!in_array($value['vendor_id'], $vendors )) {
                    $rma = $this->rmaFactory->create();
                }

                if (isset($data['rma_id']) && $data['rma_id']) {
                    $rma->load($data['rma_id']);
                }
                unset($data['rma_id']);

                if (isset($value['vendor_id'])) {
                    $userIds = $this->vendorResource->getUserIdsByVendorId($value['vendor_id']);
                    if (isset($userIds[0])) {
                        $userId = $userIds[0];
                    }
                    $data['user_id'] = $userId;
                }

                $rma = $this->updateRma($performer, $rma, $data);
                $this->itemUpdateService->updateItems($rma, [$value['order_item_id'] => $value]);
                if (!in_array($value['vendor_id'], $vendors )) {
                    $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'performer' => $performer]);
                }

                if (
                    (isset($data['reply']) && $data['reply'] != '') ||
                    (!empty($_FILES['attachment']) && !empty($_FILES['attachment']['name'][0]))
                ) {
                    if (!in_array($value['vendor_id'], $vendors )) {
                        $this->messageAddService->addMessage($performer, $rma, $data['reply'], $data);
                    }
                }

                $index++;
                $vendors[] = $value['vendor_id'];
            }
        }

        return $rma;
    }

    /**
     * @param \Mirasvit\Rma\Api\Service\Performer\PerformerInterface $performer
     * @param \Mirasvit\Rma\Api\Data\RmaInterface                    $rma
     * @param array                                                  $data
     *
     * @return \Mirasvit\Rma\Api\Data\RmaInterface
     */
    protected function updateRma($performer, $rma, $data)
    {
        if (isset($data['street2']) && $data['street2'] != '') {
            $data['street'] .= "\n".$data['street2'];
            unset($data['street2']);
        }

        $order = $this->orderAbstractFactory->get($data);
        if (!empty($data['is_offline'])) {
            $orderInfo = current($data['orders']);
            $order->getResource()->load($order, $orderInfo['order_id']);
        } else {
            $orderId = isset($data['order_ids']) ? current($data['order_ids']) : null;
            $order->getResource()->load($order, $orderId);
        }
        if (!$order->getId()) {
            $order = $this->rmaOrderService->getOrder($rma);
        }

        $rma->addData($data);
        $rma->setIfOffline($order->getIsOffline());

        $storeId = $order->getStoreId();
        if (!$storeId && isset($data['store_id'])) {
            $storeId = (int)$data['store_id'];
        }
        $customerId = $order->getCustomerId();
        if (!$customerId && isset($data['customerId'])) {
            $customerId = (int)$data['customerId'];
        }
        $rma->setCustomerId($customerId);
        $rma->setStoreId($storeId);

        if (!$order->getCustomerId() && empty($rma->getEmail())) {
            $this->setRmaCustomerInfo($rma, $performer);
        } else {
            $this->setRmaAddress($rma);
        }

        $performer->setRmaAttributesBeforeSave($rma);

        $rma->save();


        if (!$this->registry->registry('current_rma')) {
            $this->registry->register('current_rma', $rma);
        }

        return $rma;
    }

}