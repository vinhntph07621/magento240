<?php
namespace Omnyfy\Stripe\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Index
 * @package Omnyfy\Stripe\Controller\Adminhtml\Test\Index
 */
class HandleStripeResponseCodeObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Omnyfy\Stripe\Helper\Data
     */
    protected $stripeHelper;

    /**
     * HandleStripeResponseCodeObserver constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Omnyfy\Stripe\Helper\Data $stripeHelper
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Omnyfy\Stripe\Helper\Data $stripeHelper
    ) {
        $this->messageManager = $messageManager;
        $this->stripeHelper = $stripeHelper;
    }

    /**
     * Function execute
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!empty($stripeCode = $observer->getRequest()->getParam('code'))) {
            try {
                $this->stripeHelper->handleStripeAccountAuthCode($stripeCode);
                $this->messageManager->addSuccessMessage(
                    __('Your account Stripe was successfully created. To manage your account, go to your profile and check your "My bank and payout" details.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
    }
}
