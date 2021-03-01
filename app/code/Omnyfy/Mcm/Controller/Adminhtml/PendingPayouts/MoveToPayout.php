<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\Pendingpayouts;

class MoveToPayout extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $readyToPayoutId = 1;
    protected $vendorOrderFactory;
    protected $_shippingCalculationFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Mcm\Model\VendorOrderFactory $vendorOrderFactory,
        \Omnyfy\Mcm\Model\ShippingCalculationFactory  $shippingCalculationFactory
    ) {
        $this->vendorOrderFactory = $vendorOrderFactory;
        $this->_shippingCalculationFactory = $shippingCalculationFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute() {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->vendorOrderFactory->create();
                $model->load($id);
                $model->setData('payout_action', $this->readyToPayoutId);
                $model->save();

                $vendorShipments = $this->_shippingCalculationFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', $model->getOrderId())
                    ->addFieldToFilter('vendor_id', $model->getVendorId())
                    ->addFieldToFilter('ship_by_type', '2');

                // Update shipping record to ready to payout
                if ($vendorShipments->getSize() > 0) {
                    foreach($vendorShipments as $vendorShipment) {
                        $vendorShipment->setType('ready_to_payout');
                        $vendorShipment->save();
                    }
                }

                $this->messageManager->addSuccessMessage(__('The pending order in the payout has been move to Orders Included in Payout.'));
                $this->_redirect('omnyfy_mcm/pendingpayouts/view', ['vendor_id'=> $this->getRequest()->getParam('vendor_id')]);
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                        __('We can\'t move to Orders Included in Payout. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('omnyfy_mcm/pendingpayouts/view', ['vendor_id'=> $this->getRequest()->getParam('vendor_id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find the pending order in the payout to move to Orders Included in Payout.'));
        $this->_redirect('omnyfy_mcm/pendingpayouts/view', ['vendor_id'=> $this->getRequest()->getParam('vendor_id')]);
    }

}
