<?php


namespace Omnyfy\Checklist\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ArticleChecklistOption extends Template implements BlockInterface
{
    protected $_articleId;
    protected $_template = "widget/articleChecklistOption.phtml";
    protected $_checklistItemOptionsCollectionFactory;
    protected $_checklistItemUserOptionsCollectionFactory;
    protected $_customerSession;
    protected $_coreSessions;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions\CollectionFactory $checklistItemUserOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions\CollectionFactory $checklistItemOptionsCollectionFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ){
        $this->_customerSession = $customerSession;
        $this->_checklistItemUserOptionsCollectionFactory = $checklistItemUserOptionsCollectionFactory;
        $this->_checklistItemOptionsCollectionFactory = $checklistItemOptionsCollectionFactory;
        $this->_coreSessions = $coreSession;
        parent::__construct($context, $data);
    }

    public function setArticleId($articleId) {
        $this->_articleId = $articleId;
        return $this;
    }
    

    public function getArticleId() {
        return $this->_articleId;
    }

    public function getCompleteArticleUrl(){
        return $this->getUrl('checklist/update/completearticle');
    }

    public function isItemChecked($optionId){
        $customerId     = $this->getUserId();

        $userOptions = $this->_checklistItemUserOptionsCollectionFactory->create();
        $userOptions->addFieldToFilter('user_id', ['eq' => $customerId]);
        $userOptions->addFieldToFilter('option_id', ['eq' => $optionId]);
        $userOptions->load();

        if (count($userOptions) > 0 ){
            $userOption = $userOptions->getFirstItem();
            return $userOption->getData('checklistitemuseroptions_id');
        }
        return 0;
    }

    public function userLoggedDetails() {
        return $this->_customerSession->getCustomer();
    }

    public function getUserId() {
        return $this->userLoggedDetails()->getId();
    }
}
