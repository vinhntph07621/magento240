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



namespace Mirasvit\RewardsAdminUi\Controller\Adminhtml\Transaction;

use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Controller\RegistryConstants;

class Customergrid extends \Mirasvit\RewardsAdminUi\Controller\Adminhtml\Transaction
{
    /**
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id = $this->getRequest()->getParam('id');
        $customer = $this->customerFactory->create()->load($id);
        $this->registry->register('current_customer', $customer);
        $this->registry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());

        $this->getResponse()->setBody(
            $resultPage->getLayout()
                ->createBlock('\Mirasvit\RewardsAdminUi\Block\Adminhtml\Customer\Edit\Tabs\Transaction\Grid', 'rewards.grid')
                ->toHtml()
        );
    }
}
