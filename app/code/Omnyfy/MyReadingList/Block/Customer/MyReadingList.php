<?php
namespace Omnyfy\MyReadingList\Block\Customer;

class MyReadingList extends \Magento\Framework\View\Element\Template
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


    /**
     * @return \Magento\Customer\Model\SessionFactory
     */

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
	
	public function getBookMarkedCollection() {
		if ($customer_id = $this->getUserId()) {
			$responseData = $this->_readingListCollection->create();
			$responseData->getCustomerList($customer_id);
			$responseData->addListArticles();
			$responseData->populateArticleDetails();			
			$responseData->getSelect()
							->where("ca.is_active=1")
							->group('ca.article_id')
							->order('readinglist_article_id DESC')
							->limit(2);
			return $responseData;
		}
	}
} 
