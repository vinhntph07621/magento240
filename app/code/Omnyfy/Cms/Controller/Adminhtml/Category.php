<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml;

/**
 * Admin cms category edit controller
 */
class Category extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'cms_category_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Omnyfy_Cms::category';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Omnyfy\Cms\Model\Category';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Omnyfy_Cms::category';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
    
    protected $_massActionRequestKey = 'id';
}