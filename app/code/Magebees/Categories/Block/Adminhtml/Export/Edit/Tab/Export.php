<?php
namespace Magebees\Categories\Block\Adminhtml\Export\Edit\Tab;
class Export extends \Magento\Backend\Block\Widget\Form\Generic
{

	protected $_yesno;
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = array()
    ) {
		$this->_yesno = $yesno;
		$this->setTemplate('Magebees_Categories::export.phtml');
        parent::__construct($context, $registry, $formFactory, $data);
	}
	
 
	protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    } 
		
}
