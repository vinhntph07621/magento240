<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 23/6/17
 * Time: 4:50 PM
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Order\Shipment;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderRepositoryInterface;

class Start extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;

    protected $orderRepository;

    public function __construct(Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->orderRepository = $orderRepository;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }

    /**
     * Start create shipment action
     *
     * @return void
     */
    public function execute()
    {
        //load locations of current order
        // if there's only one location,
        $orderId = $this->getRequest()->getParam('order_id');

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);

        $items = $order->getItems();
        $locations = [];
        foreach($items as $item) {
            $locations[] = $item->getLocationId();
        }
        $locations = array_unique($locations);
        if (1 == count($locations)) {
//            $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
//            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_order');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipments'));
        $this->_view->renderLayout();
    }
}