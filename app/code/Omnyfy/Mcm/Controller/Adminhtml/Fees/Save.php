<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\Fees;

use Omnyfy\Mcm\Controller\Adminhtml\AbstractAction;
use Omnyfy\Mcm\Model\FeesCharges;
use Omnyfy\Mcm\Model\VendorPayout;

/**
 * Cms template controller
 */
class Save extends AbstractAction {

    protected $feesChargesModel;

    protected $payoutModel;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param FeesCharges $feesChargesModel
     * @param VendorPayout $payoutModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        FeesCharges $feesChargesModel,
        VendorPayout $payoutModel
    ) {
        $this->feesChargesModel = $feesChargesModel;
        $this->payoutModel = $payoutModel;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    /**
     * Before model save
     * @param  \Omnyfy\Cms\Model\FeesCharges $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request) {
        
    }

    public function execute() {
        $request = $this->getRequest();
        
        $id = '';
        $feeId = '';

        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }
        $session = $this->_session;
        $data = $this->getRequest()->getPostValue();

        $model = $this->feesChargesModel;
        try {
            $inputFilter = new \Zend_Filter_Input(
                    [], [], $data
            );
            $data = $inputFilter->getUnescaped();

            if (isset($data['id']) && !empty($data['id'])) {
                $feeId = $id = $data['id'];
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong fees and charges is specified.'));
                    }
                }
            }
            //$data['status'] = 1;
            $model->setData($data);
            $session->setPageData($data);
            $this->_beforeSave($model, $request);
            if ($model->save()) {
                $model = $model->load($id);
                $this->savePayoutInfo($model, $data); //Save Payout info ewallet id
                $this->_afterSave($model, $request);

                $this->messageManager->addSuccess(__('You saved the fees and charges.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $id = (int) $this->getRequest()->getParam('id');
            if (!empty($id)) {
                $this->_redirect('*/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('*/*/edit', ['id' => $feeId]);
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(
                    __('Something went wrong while saving the fees and charges data. Please review the error log.')
            );
            $this->_logger->critical($e->getMessage());
            $this->_session->setPageData($data);
            if (isset($data['id'])) {
                $this->_redirect('*/*/edit', ['id' => $data['id']]);
            } else if ($model->getId()) {
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
            } else {
                $this->_redirect('*/*/edit', ['id' => '']);
            }

            return;
        }
        $this->_redirect('*/*/');
    }

    public function savePayoutInfo($model, $data) {
        if (isset($data['ewallet_id'])) {
            $payoutModel = $this->payoutModel->load($model->getId(), 'fees_charges_id');
            $payoutModel->setEwalletId($data['ewallet_id']);
            $payoutModel->save();
        }
    }

    /**
     * After model save
     * @param  \Omnyfy\Cms\Model\FeesCharges $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _afterSave($model, $request) {
        
    }

}
