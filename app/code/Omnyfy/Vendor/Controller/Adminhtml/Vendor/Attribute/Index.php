<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-08
 * Time: 15:19
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Attribute;

class Index extends \Omnyfy\Vendor\Controller\Adminhtml\Vendor\Attribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->createActionPage();
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Omnyfy\Vendor\Block\Adminhtml\Vendor\Attribute')
        );
        return $resultPage;
    }
}