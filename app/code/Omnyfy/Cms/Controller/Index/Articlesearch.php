<?php 
namespace Omnyfy\Cms\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Omnyfy\Cms\Helper\Data;
class Articlesearch extends Action
{
	 protected $datetime;
	 /**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	protected $scopeConfig;
	
	protected $inlineTranslation;
	
	protected $transportBuilder;
	
	/** @var \Omnyfy\Events\Helper\Data */
    protected $_dataHelper;
	
    public function __construct(
        Context $context,
        DateTime $datetime,
		ScopeConfigInterface $scopeConfig,
		StateInterface $inlineTranslation,
		TransportBuilder $transportBuilder,
		Data $dataHelper,
		PageFactory $resultPageFactory
	)
    {
		$this->resultPageFactory = $resultPageFactory;
		$this->transportBuilder = $transportBuilder;
		$this->inlineTranslation = $inlineTranslation;
		$this->scopeConfig = $scopeConfig;
        $this->datetime = $datetime;
		$this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }
	
	
	/**
     * Default Events Index page
     *
     * @return void
     */
    public function execute()
    {	
		// Store post data into variable
        $data = $this->getRequest()->getPost();
		
		$article = $data['article'];
		$userType = $data['user_type'];
		$response = $this->_dataHelper->getArticleList($article,$userType);
		echo $response;
    }
}
