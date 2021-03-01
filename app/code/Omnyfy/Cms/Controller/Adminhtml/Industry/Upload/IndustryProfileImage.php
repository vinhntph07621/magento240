<?php
/**
 * Project: CMS Industry M2.
 * User: abhay
 * Date: 01/05/17
 * Time: 2:30 PM
 */
namespace Omnyfy\Cms\Controller\Adminhtml\Industry\Upload;

use Omnyfy\Cms\Controller\Adminhtml\Upload\Image\Action;

/**
 * Cms featured image upload controller
 */
class IndustryProfileImage extends Action
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'industry_profile_image';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Omnyfy_Cms::industry');
    }

}
