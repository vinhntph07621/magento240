<?php
namespace Omnyfy\Checklist\Block\Customer;

class RecentChecklist extends \Magento\Framework\View\Element\Template
{
    private $_objectManager;
    private $_checklistCollectionFactory;
    private $_checklistItemsCollectionFactory;
    private $_checklistItemOptionsCollectionFactory;
    private $_checklistItemUploadsCollectionFactory;
    private $_checklistItemUserOptionsCollectionFactory;
    private $_checklistItemUserUploadsCollectionFactory;
    private $_myCustomerSession;
    protected $_request;
    protected $_coreSessions;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Omnyfy\Checklist\Model\ResourceModel\Checklist\CollectionFactory $checklistCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItems\CollectionFactory $checklistItemsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions\CollectionFactory $checklistItemOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads\CollectionFactory $checklistItemUploadsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions\CollectionFactory $checklistItemUserOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads\CollectionFactory $checklistItemUserUploadsCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        //\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        array $data = []
    ){
        $this->_objectManager = $objectManager;
        $this->_checklistCollectionFactory = $checklistCollectionFactory;
        $this->_checklistItemsCollectionFactory = $checklistItemsCollectionFactory;
        $this->_checklistItemOptionsCollectionFactory = $checklistItemOptionsCollectionFactory;
        $this->_checklistItemUploadsCollectionFactory = $checklistItemUploadsCollectionFactory;
        $this->_checklistItemUserOptionsCollectionFactory = $checklistItemUserOptionsCollectionFactory;
        $this->_checklistItemUserUploadsCollectionFactory = $checklistItemUserUploadsCollectionFactory;
        $this->_coreSessions = $coreSession;
        $this->_request = $request;
        //$this->_fileFactory = $fileFactory;
        parent::__construct($context, $data);
        $this->_myCustomerSession = $customerSession;
    }

     public function getChecklist() {
        $checklist = $this->_checklistCollectionFactory->create()->load();
        foreach($checklist as $cl){
            $__checklist["id"] = $cl["checklist_id"];
            $__checklist["title"] = $cl["checklist_title"];
            $__checklist["checklist_status"] = $cl["checklist_status"];
            $__checklist["description"] = $cl["checklist_description"];
        }
        return $__checklist;
    }

    public function getChecklistItems($id){
        $checkListItems = $this->_checklistItemsCollectionFactory->create();
        $checkListItems->addFilter('checklist_id', ['eq' => $id]);
        return $checkListItems;
    }

    public function getChecklistItemOptions($id) {
        $checkListItemOptions = $this->_checklistItemOptionsCollectionFactory->create();
        $checkListItemOptions->addFilter('item_id', ['eq' => $id]);
        return $checkListItemOptions;
    }

    public function getChecklistItemUploads($id) {
        $checkListItemUploads = $this->_checklistItemUploadsCollectionFactory->create();
        $checkListItemUploads->addFilter('item_id', ['eq' => $id]);
        return $checkListItemUploads;
    }
	
	public function percentageComplete() {
        $id = $this->getChecklistId();
        $checklist = $this->_checklistCollectionFactory->create();
        $checklist->addFilter('ci.checklist_id', ['eq' => $id]);
        $checklist->joinChecklistItems();
        $checklist->joinChecklistItemOptions();
        $checklist->joinCmsArticles();
        $totalOptions = count($checklist);
        $complOptions = count($this->getCompletedOptions($id));

        if ($totalOptions == 0)
            return 0;
        else
            return ceil(($complOptions/$totalOptions)*100);
    }
	
	public function getCompletedOptions($checklsitId){
        $userOptions = $this->_checklistItemUserOptionsCollectionFactory->create();
        $userOptions->joinItemData();
        $userOptions->joinItemOptions ();
        $userOptions->joinCmsArticles();
        $userOptions->addFilter('user_id', ['eq' => $this->getUserId()]);
        $userOptions->addFilter('checklist_id', ['eq' => $checklsitId]);
        return $userOptions;
    }

    public function userLoggedDetails() {
        return $this->_myCustomerSession->getCustomer();
    }

    public function getUserId() {
        return $this->userLoggedDetails()->getId();
    }

	public function getChecklistUrl(){
		return $this->getUrl().'checklist?id='.$this->getChecklistId();
	}
}
