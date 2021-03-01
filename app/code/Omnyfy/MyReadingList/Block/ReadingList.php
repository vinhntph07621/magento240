<?php


namespace Omnyfy\MyReadingList\Block;

class ReadingList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_myCustomerSession;
	protected $_readingListCollection;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
		\Omnyfy\MyReadingList\Model\ResourceModel\ReadingList\CollectionFactory $readingListCollection,
        array $data = []
    ){
        $this->_myCustomerSession = $customerSession;
		$this->_readingListCollection = $readingListCollection;
        parent::__construct($context, $data);
    }

    public function isCustomerLogIn() {
        return $this->_myCustomerSession->isLoggedIn();
    }

    public function isLoggedIn() {
        return $this->_myCustomerSession->isLoggedIn();
    }

    public function userLoggedDetails() {
        return $this->_myCustomerSession->getCustomer();
    }

    public function getUserId() {
        return $this->_myCustomerSession->getCustomer()->getId();
    }
	
	public function isBookmarked($articleId) {
		if ($customer_id = $this->getUserId()) {
			$responseData = $this->_readingListCollection->create();
			$responseData->getCustomerList($customer_id);
			$responseData->addListArticles();			
			$responseData->isArticleBookMarked($articleId);
			if (count($responseData) > 0)
				//return true;
				return $responseData;			
		}
		return false;
	}

    public function getAjaxUrl(){
        return $this->getUrl("readinglist/view/view"); // Controller Url
    }

    public function getDeleteUrl() {
        return $this->getUrl("readinglist/delete/delete");
    }

    public function getAddUrl() {
        return $this->getUrl("readinglist/add/add");
    }

    public function getMyReadingList(){

    }
} 
