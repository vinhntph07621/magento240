<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-08
 * Time: 15:20
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Location\Attribute;

class Index extends \Omnyfy\Vendor\Controller\Adminhtml\Location\Attribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->createActionPage();
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Omnyfy\Vendor\Block\Adminhtml\Location\Attribute')
        );
        return $resultPage;
    }
}