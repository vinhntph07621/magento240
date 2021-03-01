<?php
/**
 * Copyright © 2017 Omnyfy. All rights reserved.
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor;

class NewAction extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendors';
    protected $resourceKey = 'Omnyfy_Vendor::vendors';

    protected $adminTitle = 'Vendors';

    public function execute()
    {
        $this->_forward('edit');
    }
}
