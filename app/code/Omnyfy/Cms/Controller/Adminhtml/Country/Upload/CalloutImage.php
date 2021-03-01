<?php

namespace Omnyfy\Cms\Controller\Adminhtml\Country\Upload;

use Omnyfy\Cms\Controller\Adminhtml\Upload\Image\Action;

/**
 * Cms featured image upload controller
 */
class CalloutImage extends Action
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'callout_image';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Omnyfy_Cms::country');
    }

}
