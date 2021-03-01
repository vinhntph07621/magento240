<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/13/2018
 * Time: 10:57 AM
 */

namespace Omnyfy\Checklist\Controller\Update;
use Magento\Framework\Controller\ResultFactory;

class CompleteArticle extends \Magento\Framework\App\Action\Action
{
    protected $_myCustomerSession;
    protected $_checklistItemOptionsCollectionFactory;
    protected $_checklistItemUserOptionsCollectionFactory;
    protected $_omnyfyChecklistBlock;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions\CollectionFactory $checklistItemOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions\CollectionFactory $checklistItemUserOptionsCollectionFactory,
        \Omnyfy\Checklist\Block\OmnyfyChecklist $omnyfyChecklistBlock
    ){
        $this->_checklistItemOptionsCollectionFactory = $checklistItemOptionsCollectionFactory;
        $this->_checklistItemUserOptionsCollectionFactory = $checklistItemUserOptionsCollectionFactory;
        $this->_myCustomerSession = $customerSession;
        $this->_omnyfyChecklistBlock = $omnyfyChecklistBlock;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $optionId  = $this->getRequest()->getParam('option_id',0);
            $itemId  = $this->getRequest()->getParam('item_id',0);
            $customerId = $this->getCustomerId();

            if ($customerId) {
                $message = "";

                $isChecked = $this->_omnyfyChecklistBlock->isItemChecked($optionId,$itemId);

                if ($isChecked == 0) {
                    $data = [
                        "user_id" => $customerId,
                        "item_id" => $itemId,
                        "option_id" => $optionId,
                    ];
                    $this->_checklistItemUserOptionsCollectionFactory->saveOptions($data);
                    $message = "Your checklist has been updated";
                    $type = "success";
                } else {
                    if ($this->_checklistItemUserOptionsCollectionFactory->deleteOption($isChecked)) {
                        $message = "Your checklist has been saved";
                        $type = "success";
                    } else {
                        $message .= "Error updating your checklist. Please try again";
                        $type = "error";
                    }
                }

            } else {
                $message = "Please login to update the checklist";
                $type = "error";
            }

            return $resultJson->setData([
                "message" => $message,
                "type" => $type
            ]);
        } catch(\Exception $e) {
            return $resultJson->setData([
                "message" => $e->getMessage(),
                "type" => "error"
            ]);
        }
    }

    public function getCustomerId() {
        return $this->_myCustomerSession->getCustomer()->getId();
    }
}