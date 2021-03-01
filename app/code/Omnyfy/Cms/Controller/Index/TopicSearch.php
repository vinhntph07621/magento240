<?php
namespace Omnyfy\Cms\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class TopicSearch extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
	
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
	/**
     * Default Article Search page
     *
     * @return void
     */
    public function execute()
    {	
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
		
		$resultRedirect = $this->resultPageFactory ->create();          
		$blockInstance = $resultRedirect->getLayout()->getBlock('cms.topic.search');
		$this->getResponse()->setBody($blockInstance->toHtml());
    }
}
