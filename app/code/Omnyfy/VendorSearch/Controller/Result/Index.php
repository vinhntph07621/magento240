<?php


namespace Omnyfy\VendorSearch\Controller\Result;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Omnyfy\VendorSearch\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Omnyfy\VendorSearch\Helper\Data $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($this->_helperData->getPageTitle());
        return $resultPage;
    }
}
