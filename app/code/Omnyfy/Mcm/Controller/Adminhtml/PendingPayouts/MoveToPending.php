<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\Pendingpayouts;

class MoveToPending extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $pendingId = 0;
    protected $vendorOrderFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Mcm\Model\VendorOrderFactory $vendorOrderFactory
    ) {
        $this->vendorOrderFactory = $vendorOrderFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute() {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->vendorOrderFactory->create();
                $model->load($id);
                $model->setData('payout_action', $this->pendingId);
                $model->save();
                $this->messageManager->addSuccessMessage(__('The order included in the payout has been move to pending.'));
                $this->_redirect('omnyfy_mcm/pendingpayouts/view', ['vendor_id'=> $this->getRequest()->getParam('vendor_id')]);
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                        __('We can\'t move to pending the order included in the payout. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('omnyfy_mcm/pendingpayouts/view', ['vendor_id'=> $this->getRequest()->getParam('vendor_id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find the order included in the payout to move to pending.'));
        $this->_redirect('omnyfy_mcm/pendingpayouts/view', ['vendor_id'=> $this->getRequest()->getParam('vendor_id')]);
    }

}
