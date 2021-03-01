<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-06
 * Time: 13:40
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Type;

class Save extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    protected $_vendorTypeFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\VendorTypeFactory $vendorTypeFactory
    )
    {
        $this->_vendorTypeFactory = $vendorTypeFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        if (! $this->getRequest()->getPostValue()) {
            $this->_redirect('omnyfy_vendor/*/');
            return;
        }

        $data = $this->getRequest()->getPostValue();

        try{
            $model = $this->_vendorTypeFactory->create();

            $inputFilter = new \Zend_Filter_Input([], [], $data);

            $data = $inputFilter->getUnescaped();
            $id = $this->getRequest()->getParam('id');
            if (empty($id) && isset($data['type_id'])) {
                $id = intval($data['type_id']);
            }

            if ($id) {
                $model->load($id);
                if ($id != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The wrong vendor type is specified.'));
                }
            }

            $this->_eventManager->dispatch('omnyfy_vendor_type_form_validation', ['form_data' => $data]);

            $model->setData($data);

            $this->_session->setPageData($model->getData());
            $model->save();

            $this->_eventManager->dispatch('omnyfy_vendor_type_form_save_after',
                [
                    'vendor_type' => $model,
                    'form_data' => $data
                ]
            );

            if ($model->getId()) {

            }

            $this->messageManager->addSuccessMessage(__('You saved the vendor type'));
            $this->_session->setPageData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('omnyfy_vendor/*/edit', ['id' => $model->getId()]);
                return;
            }
            $this->_redirect('omnyfy_vendor/*/');
            return;
        }
        catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int) $this->getRequest()->getParam('id');
            if (!empty($id)) {
                $this->_redirect('omnyfy_vendor/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('omnyfy_vendor/*/new');
            }
            return;
        }
        catch(\Exception $e)
        {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the vendor data. Please review the error log.')
            );
            $this->_logger->critical($e);
            $this->_session->setPageData($data);
            $id = (int) $this->getRequest()->getParam('id');
            $p = empty($id) ? [] : ['id' => $id];
            $this->_redirect('omnyfy_vendor/*/edit', $p);
            return;
        }
    }
}
 