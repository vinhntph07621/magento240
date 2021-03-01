<?php
namespace Omnyfy\Cms\Controller\Adminhtml\Tool\Template\Upload;

use Omnyfy\Cms\Controller\Adminhtml\Upload\Image\Action;

/**
 * Cms featured image upload controller
 */
class Icon extends Action
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'icon';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Omnyfy_Cms::tool_template');
    }

}
