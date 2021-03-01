<?php
namespace Omnyfy\VendorSignUp\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;

abstract class SignUp extends \Magento\Backend\App\Action
{
    protected $_logger;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $resultPageFactory;

    protected $signUpFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Omnyfy\VendorSignUp\Model\SignUpFactory $signUpFactory
    ) {
        $this->_logger = $logger;
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->signUpFactory = $signUpFactory;
        parent::__construct($context);
    }

    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Omnyfy_VendorSignUp::omnyfy_vendorsignup_listing')->_addBreadcrumb(__('Manage Vendor SignUp'), __('Manage Vendor SignUp'));
        return $this;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Omnyfy_VendorSignUp::omnyfy_vendorsignup_listing');
    }
    
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}