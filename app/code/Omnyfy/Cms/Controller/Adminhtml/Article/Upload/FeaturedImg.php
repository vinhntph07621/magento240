<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml\Article\Upload;

use Omnyfy\Cms\Controller\Adminhtml\Upload\Image\Action;

/**
 * Cms featured image upload controller
 */
class FeaturedImg extends Action
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'featured_img';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Omnyfy_Cms::article');
    }

}
