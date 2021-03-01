<?php
namespace Magebees\Categories\Block\Adminhtml\Import\Edit\Tab;

class Import extends \Magento\Backend\Block\Widget\Form\Generic
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->setTemplate('Magebees_Categories::import.phtml');
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

}
