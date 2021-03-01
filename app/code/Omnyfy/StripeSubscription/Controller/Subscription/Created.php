<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-15
 * Time: 13:26
 */
namespace Omnyfy\StripeSubscription\Controller\Subscription;

class Created extends \Omnyfy\StripeSubscription\Controller\AbstractAction
{
    protected $_queueTopic = 'stripe_subscription_updated';
}
 