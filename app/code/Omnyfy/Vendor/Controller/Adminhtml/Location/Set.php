<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 16:30
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location;

abstract class Set extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::location_sets';

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
            $this->_objectManager->create('Omnyfy\Vendor\Model\Location')->getResource()->getTypeId()
        );
    }
}
 