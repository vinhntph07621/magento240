<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 16/11/18
 * Time: 3:59 PM
 */
namespace Omnyfy\Vendor\Controller\Store;

use Magento\Framework\App\Action\Context;

class Edit extends \Magento\Framework\App\Action\Action
{
    protected $helper;

    protected $allStores;

    protected $_coreRegistry;

    public function __construct(Context $context,
        \Omnyfy\Vendor\Helper\Data $helper,
        \Magento\Framework\Registry $_coreRegistry
    )
    {
        $this->helper = $helper;
        $this->_coreRegistry = $_coreRegistry;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            if (!$this->getRequest()->isAjax()) {
                throw new \Magento\Framework\Exception\SecurityViolationException(__('Invalid Request'));
            }

            $this->_coreRegistry->register('all_stores', $this->getAllStores());
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        }
        catch(\Exception $e) {
            $this->getResponse()->setHttpResponseCode(406);
            $this->getResponse()->setBody(__('Invalid request'));
            return;
        }
    }

    protected function getAllStores()
    {
        if (null == $this->allStores) {
            $this->allStores = $this->helper->getAllStores();
        }
        return $this->allStores;
    }
}