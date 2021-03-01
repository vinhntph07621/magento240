<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-11
 * Time: 15:58
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action;

abstract class Set extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_sets';

    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    protected function _setTypeId()
    {
        $this->_coreRegistry->register(
            'entityType',
            $this->_objectManager->create('Omnyfy\Vendor\Model\Vendor')->getResource()->getTypeId()
        );
    }
}
 