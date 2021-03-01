<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-10
 * Time: 10:23
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Type;

use Omnyfy\Vendor\Controller\Adminhtml\AbstractAction;

class NewAction extends AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_types';

    protected $resourceKey = 'Omnyfy_Vendor::vendor_types';

    protected $adminTitle = 'New Vendor Type';

    public function execute()
    {
        $this->_forward('edit');
    }
}
 