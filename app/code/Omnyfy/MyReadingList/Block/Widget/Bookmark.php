<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 27/07/2018
 * Time: 4:40 PM
 */

namespace Omnyfy\MyReadingList\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Bookmark extends Template implements BlockInterface
{
    protected $_articleId;
    protected $_template = "widget/bookmark.phtml";
    protected $_customerSession;
    protected $_coreSessions;
    protected $_readingListCollection;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Customer\Model\Session $customerSession,
        \Omnyfy\MyReadingList\Model\ResourceModel\ReadingList\CollectionFactory $readingListCollection,
        array $data = []
    ){
        $this->_customerSession = $customerSession;
        $this->_coreSessions = $coreSession;
        $this->_readingListCollection = $readingListCollection;

        parent::__construct($context, $data);
    }

    public function setArticleId($articleId) {
        $this->_articleId = $articleId;
        return $this;
    }

    public function getArticleId() {
        return $this->_articleId;
    }

    public function getUserId() {
        return $this->_customerSession->getCustomer()->getId();
    }

    public function isBookmarked() {
        if ($customerId = $this->getUserId()) {
            if ($articleId = $this->getArticleId()) {

                $responseData = $this->_readingListCollection->create();
                $responseData->getCustomerList($customerId);
                $responseData->addListArticles();
                $responseData->isArticleBookMarked($articleId);

                if (count($responseData) > 0)
                    return true;
            }
        }
        return false;
    }
}