<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-08-05
 * Time: 14:29
 */
namespace Omnyfy\VendorSignUp\Controller\Index;

class Success extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

    }

    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
 