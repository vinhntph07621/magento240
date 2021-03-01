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


namespace Mirasvit\RewardsCustomerAccount\Controller\Account;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Subscribe extends \Mirasvit\RewardsCustomerAccount\Controller\Account
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(
        \Mirasvit\RewardsCustomerAccount\Helper\Account\Rule $accountRuleHelper,
        \Mirasvit\Rewards\Model\Config $config,
        CustomerRepository $customerRepository,
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->customerRepository = $customerRepository;

        parent::__construct($accountRuleHelper, $config, $transactionFactory, $registry, $customerSession, $context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
        } else {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute('rewards_subscription', 1);

            $this->customerRepository->save($customer);

            $this->messageManager
                ->addSuccessMessage(__('We subscribed you on expiring points notification.'));
        }

        $this->_redirect('rewards/account/history');
    }
}
