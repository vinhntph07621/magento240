<?php
namespace Omnyfy\VendorSignUp\Model;

use \Magento\Config\Model\Config\CommentInterface;

class VendorSignUpComment implements CommentInterface
{
    protected $_backendUrl;

    public function __construct(
        \Magento\Backend\Model\UrlInterface $backendUrl
    )
    {
        $this->_backendUrl = $backendUrl;
    }

    public function getCommentText($elementValue)
    {
        $__link = $this->_backendUrl->getUrl("admin/email_template/index");
        return 'A notification email sent to admin once receiving a new vendor signup. <a href="'.$__link.'">Click here to create a new one.</a>';
    }
}