<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Adminhtml\Question;

class NewAction extends \Amasty\Faq\Controller\Adminhtml\AbstractQuestion
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
