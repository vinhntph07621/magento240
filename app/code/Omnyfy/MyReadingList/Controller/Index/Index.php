<?php
 
namespace Omnyfy\MyReadingList\Controller\Index;
 
use Magento\Framework\App\Action\Context;
 
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
	protected $_resultForwardFactory;

    public function __construct(Context $context, 
				\Magento\Framework\View\Result\PageFactory $resultPageFactory,
				\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory )
    {
        $this->_resultPageFactory    = $resultPageFactory;
		$this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
	
	public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        return parent::dispatch($request);
    }
 
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}