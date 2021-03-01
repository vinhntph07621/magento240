<?php


namespace Omnyfy\Checklist\Controller\Update;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    protected $_checklistItemUserOptionsCollectionFactory;
    protected $_myCustomerSession;
    protected $_checklistCollectionFactory;
    protected $_checklistDocumentsCollectionFactory;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions\CollectionFactory $checklistItemUserOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\Checklist\CollectionFactory $checklistCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistDocuments\CollectionFactory $checklistDocumentCollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_checklistItemUserOptionsCollectionFactory = $checklistItemUserOptionsCollectionFactory;
        $this->_checklistCollectionFactory = $checklistCollectionFactory;
        $this->_myCustomerSession = $customerSession;
        $this->_checklistDocumentsCollectionFactory = $checklistDocumentCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $optionid  = $this->getRequest()->getParam('optionid',false);
            $item_id  = $this->getRequest()->getParam('itemid',false);
            $checklist_id  = $this->getRequest()->getParam('checklistid',false);

            $customerId= $this->getCustomerId();
            $type = "success";
            $message = "";
            $percentage = 0;

            if($customerId) {
                $userOptions = $this->_checklistItemUserOptionsCollectionFactory->create();
                $userOptions->addFilter('user_id', ['eq' => $customerId]);
                $userOptions->addFilter('option_id', ['eq' => $optionid]);

                if ((count($userOptions)) <= 0 ) {
                    $data = [
                        'user_id' => $customerId,
                        'item_id' => $item_id,
                        'option_id' => $optionid
                    ];
                    $this->_checklistItemUserOptionsCollectionFactory->saveOptions($data);
                    $message = "Your checklist has been saved";
                }else {
                    $userOptionId = $userOptions->getData()[0]['checklistitemuseroptions_id'];
                    if ($this->_checklistItemUserOptionsCollectionFactory->deleteOption($userOptionId)) {
                        $message = "Your checklist has been saved";
                    }else{
                        $message = "Error updating your checklist. Please try again";
                        $type = "error";
                    }
                }
                $percentage = $this->percentageComplete($checklist_id);
            }else{
                $message = 'Your Session is expired. Please login to update';
            }

            return $resultJson->setData([
                "type" => $type,
                "message" => $message,
                "percentage" => $percentage,
                "percentagetext" => ($percentage == 0?"Get started":"complete"),
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                "message" => $e->getMessage(),
                "type" => "error"
            ]);
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    public function getCustomerId() {
        return $this->_myCustomerSession->getCustomer()->getId();
    }

    public function percentageComplete($id) {
        $checklist = $this->_checklistCollectionFactory->create();
        $checklist->addFilter('ci.checklist_id', ['eq' => $id]);
        $checklist->joinChecklistItems();
        $checklist->joinChecklistItemOptions();
        $checklist->joinCmsArticles();
        $totalOptions = count($checklist);
        $complOptions = count($this->getCompletedOptions($id));

        return ceil(($complOptions/$totalOptions)*100);
    }

    public function getCompletedOptions($id){
        $userOptions = $this->_checklistItemUserOptionsCollectionFactory->create();
        $userOptions->joinItemData();
        $userOptions->joinItemOptions ();
        $userOptions->joinCmsArticles();
        $userOptions->addFilter('user_id', ['eq' => $this->getCustomerId()]);
        $userOptions->addFilter('checklist_id', ['eq' => $id]);
        return $userOptions;
    }
}
