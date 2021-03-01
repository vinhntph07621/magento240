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



namespace Mirasvit\RewardsCheckout\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;

class ApplyPointsPost extends \Mirasvit\RewardsCheckout\Controller\Checkout
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $response = $this->processRequest();
        if ($this->getRequest()->isXmlHttpRequest()) {
            echo json_encode($response);
            exit;
        }
        if ($response['success']) {
            $this->messageManager->addSuccessMessage($response['message']);
        } elseif ($response['message']) {
            $this->messageManager->addErrorMessage($response['message']);
        }
        return $this->_goBack();
    }
}
