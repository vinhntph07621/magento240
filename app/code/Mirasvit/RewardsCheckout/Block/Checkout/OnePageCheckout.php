<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\RewardsCheckout\Block\Checkout;

/**
 * Class OnePageCheckout
 * @package Mirasvit\Rewards\Block\Checkout
 * @deprecated
 */
class OnePageCheckout extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;
    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    private $rewardsPurchase;
    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    private $rewardsBalance;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;
    private $checkoutBlock;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->moduleManager   = $moduleManager;
        $this->sessionFactory  = $sessionFactory;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->rewardsBalance  = $rewardsBalance;
        $this->customerSession = $customerSession;
        $this->context         = $context;

        parent::__construct($context, $customerSession, $checkoutSession, $data);

        $this->checkoutBlock = $this->getCheckoutBlock();
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getCheckoutBlock()
    {
        $layout = $this->context->getLayout();
        if ($this->moduleManager->isEnabled('Magecheckout_SecureCheckout')) {
            return $layout->createBlock(
                'Mirasvit\Rewards\Block\Checkout\Onepage\SecureCheckout',
                'magecheckout.securecheckout.rewards.points'
            );
        }
        if ($this->moduleManager->isEnabled('Magestore_OneStepCheckout')) {
            return $layout->createBlock(
                'Mirasvit\Rewards\Block\Checkout\Onepage\Magestore',
                'magestore.rewards.points'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _toHtml()
    {
        if (!$this->sessionFactory->create()->isLoggedIn()) {
            return '';
        }

        return $this->checkoutBlock->toHtml();
    }
}
