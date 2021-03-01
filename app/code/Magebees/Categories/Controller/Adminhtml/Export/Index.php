<?php
namespace Magebees\Categories\Controller\Adminhtml\Export;

class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magebees_Categories::export');
        $this->_addBreadcrumb(__('Export Categories'), __('Export Categories'));
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Categories::export');
    }
}
