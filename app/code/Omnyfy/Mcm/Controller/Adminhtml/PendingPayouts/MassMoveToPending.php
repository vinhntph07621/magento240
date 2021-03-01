<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\PendingPayouts;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\CollectionFactory as VendorOrderCollectionFactory;

/**
 * Class MassProcessPayouts
 */
class MassMoveToPending extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    protected $vendorOrderCollectionFactory;

    protected $pendingId = 0;

    /**
     * MassMoveToPending constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param Filter $filter
     * @param VendorOrderCollectionFactory $vendorOrderCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        Filter $filter,
        VendorOrderCollectionFactory $vendorOrderCollectionFactory
    ) {
        $this->filter = $filter;
        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute() {
        $collection = $this->filter->getCollection($this->vendorOrderCollectionFactory->create());
        $vendorId = $this->getRequest()->getParam('vendor_id');
        if (empty($vendorId)) {
            $vendorId = $this->_session->getCurrentVendorId();
        }
        if (!empty($vendorId)) {
            $collection->addFieldToFilter('vendor_id', $vendorId);
        }

        $payoutsUpdated = 0;
        foreach ($collection as $payout) {
            $payout->setData(
                    'payout_action', $this->pendingId
            );
            $payout->save();
            $payoutsUpdated++;
        }

        if ($payoutsUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $payoutsUpdated));
        }

        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('omnyfy_mcm/pendingpayouts/view', ['vendor_id'=> $vendorId]);
        return $resultRedirect;
    }
}
