<?php


namespace Omnyfy\Checklist\Block;

class OmnyfyChecklist extends \Magento\Framework\View\Element\Template
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
        if ($id = $this->getChecklistId()) {
            $this->setChecklist();
            $checklist = $this->_checklistCollectionFactory->create();
            $checklist->addFilter('checklist_id', ['eq' => $id]);

            $__checklist = array();
            foreach ($checklist as $cl) {
                $__checklist["id"] = $cl["checklist_id"];
                $__checklist["title"] = $cl["checklist_title"];
                $__checklist["description"] = $cl["checklist_description"];
                $__checklist["checklist_status"] = $cl["checklist_status"];
            }
            return $__checklist;
        }
        return null;
    }

    public function isChecklistEnable(){
        $__checklist = $this->getChecklist();
        if ($__checklist) {
            if($__checklist["checklist_status"] == 1 )
                return true;
        }
        return false;
    }

    public function getChecklistItems(){
        $id = $this->getChecklistId();
        $checkListItems = $this->_checklistItemsCollectionFactory->create();
        $checkListItems->addFilter('checklist_id', ['eq' => $id]);
        return $checkListItems;
    }

    public function getChecklistItemOptions($id) {
        $checkListItemOptions = $this->_checklistItemOptionsCollectionFactory->create();
        $checkListItemOptions->addFilter('main_table.item_id', ['eq' => $id]);
        $checkListItemOptions->joinCmsArticles();
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

    public function getUploadedFiles($item_id) {
        $uploadedFiles = $this->_checklistItemUserUploadsCollectionFactory->create();
        $uploadedFiles->addFilter('user_id', ['eq' => $this->getUserId()]);
        $uploadedFiles->addFilter('item_id', ['eq' => $item_id]);
        return $uploadedFiles;
    }

    public function getDownloadLink($filename){

    }

    public function getCompletedOptionsArray(){
        $id = $this->getChecklistId();
        return $this->getCompletedOptions($id)->getColumnValues('option_id');
    }

    public function isLoggedIn() {
        return $this->_myCustomerSession->isLoggedIn();
    }

    public function userLoggedDetails() {
        return $this->_myCustomerSession->getCustomer();
    }

    public function getUserId() {
        return $this->userLoggedDetails()->getId();
    }

    public function getItemOptionUpdateUrl() {
        return $this->getUrl('checklist/update/index');
    }

    public function getFileUploadUrl(){
        return $this->getUrl('checklist/upload/index');
    }

    public function getServiceProviderSearchUrl() {
        return $this->getUrl("servicesearch/index/index");
    }

    public function getChecklistId(){
        return $this->_request->getParam('id');
    }

    public function setChecklist(){
        $this->_coreSessions->start();
        $this->_coreSessions->setChecklist($this->getChecklistId());
    }

    public function getChecklistValue(){
        $this->_coreSessions->start();
        return $this->_coreSessions->getChecklist();
    }

    public function unSetChecklistValue(){
        $this->_coreSessions->start();
        return $this->_coreSessions->unsChecklist();
    }

    public function isItemChecked($optionId, $itemId){
        $customerId     = $this->getUserId();

        $userOptions = $this->_checklistItemUserOptionsCollectionFactory->create();
        $userOptions->addFieldToFilter('user_id', ['eq' => $customerId]);
        $userOptions->addFieldToFilter('item_id', ['eq' => $itemId]);
        $userOptions->addFieldToFilter('option_id', ['eq' => $optionId]);
        $userOptions->load();

        if (count($userOptions) > 0 ){
            $userOption = $userOptions->getFirstItem();
            return $userOption->getData('checklistitemuseroptions_id');
        }
        return 0;
    }

    public function isArticledChecked($articleId) {
        $customerId     = $this->getUserId();

        if ($customerId) {
            $checklistOptions = $this->_checklistItemOptionsCollectionFactory->create();
            $checklistOptions->addFilter('cms_article_link', ['eq' => $articleId]);

            foreach ($checklistOptions as $option) {
                $userOptions = $this->_checklistItemUserOptionsCollectionFactory->create();
                $userOptions->addFieldToFilter('user_id', ['eq' => $customerId]);
                $userOptions->addFieldToFilter('item_id', ['eq' => $option->getItemId()]);

                if (count($userOptions) > 0) {
                    return 1;
                }
            }
        }
        return 0;
    }
}
