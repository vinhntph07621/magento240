<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Controller\Adminhtml\Order\Attributes;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Amasty_Orderattr::attribute_value_edit';

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($id);
        $this->coreRegistry->register('sales_order', $order);
        $this->coreRegistry->register('current_order', $order);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->getConfig()->getTitle()
            ->prepend(
                __('Edit Attributes For The Order #%1', $order->getIncrementId())
            );

        return $resultPage;
    }
}
