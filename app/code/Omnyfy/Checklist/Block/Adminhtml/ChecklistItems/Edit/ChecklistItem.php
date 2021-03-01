<?php

namespace Omnyfy\Checklist\Block\Adminhtml\ChecklistItems\Edit;


class ChecklistItem extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'Omnyfy_Checklist::checklist_item_detail.phtml';
    private $_checklistCollectionFactory;
    private $_checklistItemsCollectionFactory;
    private $_checklistItemOptionsCollectionFactory;
    private $_checklistItemUploadsCollectionFactory;
    private $_checklistItemUserOptionsCollectionFactory;
    private $_checklistItemUserUploadsCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Omnyfy\Checklist\Model\ResourceModel\Checklist\CollectionFactory $checklistCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItems\CollectionFactory $checklistItemsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions\CollectionFactory $checklistItemOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads\CollectionFactory $checklistItemUploadsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions\CollectionFactory $checklistItemUserOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads\CollectionFactory $checklistItemUserUploadsCollectionFactory,
        array $data = [])
    {
        $this->_checklistCollectionFactory = $checklistCollectionFactory;
        $this->_checklistItemsCollectionFactory = $checklistItemsCollectionFactory;
        $this->_checklistItemOptionsCollectionFactory = $checklistItemOptionsCollectionFactory;
        $this->_checklistItemUploadsCollectionFactory = $checklistItemUploadsCollectionFactory;
        $this->_checklistItemUserOptionsCollectionFactory = $checklistItemUserOptionsCollectionFactory;
        $this->_checklistItemUserUploadsCollectionFactory = $checklistItemUserUploadsCollectionFactory;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
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

    /*protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }*/
}