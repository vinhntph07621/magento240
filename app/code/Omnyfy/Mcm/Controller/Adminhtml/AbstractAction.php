<?php

namespace Omnyfy\Mcm\Controller\Adminhtml;

abstract class AbstractAction extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;

    protected $resultForwardFactory;

    protected $resultPageFactory;

    protected $authSession;

    public $_logger;

    protected $resourceKey;

    protected $adminTitle;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->authSession = $authSession;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu($this->resourceKey)->_addBreadcrumb(__($this->adminTitle), __($this->adminTitle));
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->resourceKey);
    }
}