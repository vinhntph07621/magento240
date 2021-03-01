<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-02
 * Time: 14:21
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml;


use Magento\Backend\App\Action;

abstract class AbstractAction extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    protected $_log;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_log = $logger;
        parent::__construct($context);
    }
}
 