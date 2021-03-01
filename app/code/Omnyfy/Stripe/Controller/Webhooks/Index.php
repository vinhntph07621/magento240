<?php
namespace Omnyfy\Stripe\Controller\Webhooks;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $webhooks;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Omnyfy\Stripe\Helper\Webhooks $webhooks
    )
    {
        parent::__construct($context);
        $this->webhooks = $webhooks;
    }

    public function execute()
    {
        $this->webhooks->dispatchEvent();
    }
}
