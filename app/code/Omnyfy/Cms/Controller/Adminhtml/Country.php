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
class Country extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'cms_country_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Omnyfy_Cms::country';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Omnyfy\Cms\Model\Country';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Omnyfy_Cms::country';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'status';
    
    protected $_massActionRequestKey = 'selected';
}