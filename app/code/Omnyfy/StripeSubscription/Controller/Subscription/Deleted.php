<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 00:17
 */
namespace Omnyfy\StripeSubscription\Controller\Subscription;

class Deleted extends \Omnyfy\StripeSubscription\Controller\AbstractAction
{
    protected $_queueTopic  = 'stripe_subscription_deleted';
}
 