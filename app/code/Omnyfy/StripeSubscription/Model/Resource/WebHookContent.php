<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 11:24
 */
namespace Omnyfy\StripeSubscription\Model\Resource;

class WebHookContent extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_stripe_webhook_content', 'id');
    }

    protected function getUpdateFields()
    {
        return [
            'content'
        ];
    }
}
 