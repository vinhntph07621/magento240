<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml;

/**
 * Admin cms article edit controller
 */
class Article extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'cms_article_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Omnyfy_Cms::article';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Omnyfy\Cms\Model\Article';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Omnyfy_Cms::article';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
    
    protected $_massActionRequestKey = 'id';

}
