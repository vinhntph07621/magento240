<?php
/**
 * Project: CMS M2.
 * User: abhay
 * Date: 01/06/18
 * Time: 11:00 AM
 */

namespace Omnyfy\Cms\Controller\ToolTemplate;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $resultForwardFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    { 
        $this->_view->loadLayout();
		$this->_view->getLayout()->initMessages();
		$resultPage = $this->resultPageFactory->create();		
		return $resultPage;
    }
}