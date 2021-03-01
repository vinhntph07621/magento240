<?php

/**
 * Copyright © 2015 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml\User;

use Omnyfy\Cms\Controller\Adminhtml\Actions;
use Magento\Framework\Controller\ResultFactory;

/**
 * Admin cms article edit controller
 */
class Type extends Actions {

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Omnyfy_Cms::user_type';

    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey = 'cms_user_type_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey = 'Omnyfy_Cms::user_type';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass = 'Omnyfy\Cms\Model\UserType';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu = 'Omnyfy_Cms::user_type';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField = 'status';
    
    /**
     * Request id key
     * @var string
     */
    protected $_idKey = 'id';
    
    protected $_massActionRequestKey = 'selected';
    
    
}
