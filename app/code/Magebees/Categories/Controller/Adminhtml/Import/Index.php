<?php
namespace Magebees\Categories\Controller\Adminhtml\Import;

class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magebees_Categories::import');
        $this->_addBreadcrumb(__('Import Categories'), __('Import Categories'));
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Categories::import');
    }
}
