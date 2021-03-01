<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 18/01/2019
 * Time: 9:59 AM
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Location;


class Report extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Stock Report by Location')));

        return $resultPage;
    }


}