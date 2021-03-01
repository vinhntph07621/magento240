<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\Fees;

use Magento\Backend\App\Action\Context;
use Omnyfy\Mcm\Model\ResourceModel\FeesCharges\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassStatus
 */
class MassStatus extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    /**
     * @var Omnyfy\RequestQuote\Model\ResourceModel\RequestQuoteTemplate\CollectionFactory;
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    /**
     * Execute controller
     * @return Magento\Framework\Controller\ResultFactor
     */
    public function execute() {

        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $feeStatus = $this->getRequest()->getParam('status');

        $feesUpdated = 0;
        foreach ($collection as $fee) {
            $fee->setData(
                    'status', $feeStatus
            );
            $fee->save();
            $feesUpdated++;
        }

        if ($feesUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $feesUpdated));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('omnyfy_mcm/fees/index');
        return $resultRedirect;
    }

}
