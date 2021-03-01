<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 7/8/17
 * Time: 9:51 AM
 */
namespace Omnyfy\Vendor\Controller\Brands;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}