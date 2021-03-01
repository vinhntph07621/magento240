<?php
namespace Omnyfy\Cms\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Search extends \Magento\Framework\App\Action\Action
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
        /* $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
		
		$resultRedirect = $this->resultPageFactory ->create();          
		$blockInstance = $resultRedirect->getLayout()->getBlock('cms.learn.search');
		$this->getResponse()->setBody($blockInstance->toHtml()); */
		$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
		
        $listBlock = $this->_view->getLayout()->getBlock('cms.learn.search');
		$resultPage = $this->resultPageFactory->create();
		
		// Add page title
        // Add page title
		if($this->getRequest()->getParam('article-search')){
			$resultPage->getConfig()->getTitle()->set(__('Search "%1"',  urldecode($this->getRequest()->getParam('article-search'))));
		} else{
			$resultPage->getConfig()->getTitle()->set(__('Search %1',  urldecode($this->getRequest()->getParam('article-search'))));
		}
		// Add breadcrumb
		/** @var \Magento\Theme\Block\Html\Breadcrumbs */
		/* $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb('home', [
			'label' => __('Home'),
			'title' => __('Home'),
			'link' => $this->_url->getUrl('')
				]
		);
		$breadcrumbs->addCrumb('cms', [
			'label' => __('Learn'),
			'title' => __('Learn')
				]
		);
		if(urldecode($this->getRequest()->getParam('article-search'))){
			$breadcrumbs->addCrumb('search_link', [
				'label' => __(urldecode($this->getRequest()->getParam('article-search'))),
				'title' => __(urldecode($this->getRequest()->getParam('article-search')))
					]
			);
		} */
			
		/* $resultPage->getConfig()->getTitle()->set('All Events'); */
        if ($listBlock) {
            $currentPage = abs(intval($this->getRequest()->getParam('p')));
            if ($currentPage < 1) {
                $currentPage = 1;
            }
            
            $listBlock->setCurrentPage($currentPage);
        }
        
		/** @var \Magento\Framework\View\Result\Page $resultPage */
        return $resultPage;
    }
}
