<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 5/2/18
 * Time: 12:03 AM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Inventory;

class InlineEdit extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::inventory';

    protected $resultJsonFactory;

    protected $inventoryResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->inventoryResource = $inventoryResource;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach ($postItems as $inventoryId => $item) {
            $this->inventoryResource->updateQty($inventoryId, $item['qty']);
        }

        return $resultJson->setData([
            'messages' => $this->getErrorMessages(),
            'error' => $this->isErrorExists()
        ]);
    }

    protected function getErrorMessages() {
        $messages = [];
        foreach($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    protected function isErrorExists() {
        return (bool) $this->getMessageManager()->getMessages(true)->getCount();
    }
}