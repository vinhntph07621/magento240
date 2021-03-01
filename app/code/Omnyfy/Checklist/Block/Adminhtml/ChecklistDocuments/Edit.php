<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/4/2018
 * Time: 9:32 AM
 */

namespace Omnyfy\Checklist\Block\Adminhtml\ChecklistDocuments;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    private $_checklistDocuments;
    private $_userUploadsCollectionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistDocuments\CollectionFactory $checklistdocuments,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads\CollectionFactory $userUploads,
        array $data = []
    ) {
        $this->_checklistDocuments = $checklistdocuments;
        $this->_userUploadsCollectionFactory = $userUploads;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'checklistdocument_id';
        $this->_controller = 'Adminhtml_ChecklistDocuments';
        $this->_blockGroup = 'Omnyfy_Checklist';

        parent::_construct();
    }

    /**
     * Retrieve text for header element depending on loaded news
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Checklist Documents');
    }

    public function getChecklistDocument() {
        $docId = $this->getRequest()->getParam('checklistdocument_id');
        $documents = $this->_checklistDocuments->create();
        $documents->addFilter('checklistdocument_id', ['eq' => $docId]);

        return $documents;
    }

    public function getUploadedFiles($user_id, $checklist_id) {
        $uploadedFiles = $this->_userUploadsCollectionFactory ->create();
        $uploadedFiles->joinChecklistItems();
        $uploadedFiles->joinChecklistItemUploads();
        $uploadedFiles->addFilter('user_id', ['eq' => $user_id]);
        $uploadedFiles->addFilter('ci.checklist_id', ['eq' => $checklist_id]);
        return $uploadedFiles;

    }
}