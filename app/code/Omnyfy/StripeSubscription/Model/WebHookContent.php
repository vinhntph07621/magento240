<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 11:21
 */
namespace Omnyfy\StripeSubscription\Model;

class WebHookContent extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Omnyfy\StripeSubscription\Model\Resource\WebHookContent');
    }
}
 