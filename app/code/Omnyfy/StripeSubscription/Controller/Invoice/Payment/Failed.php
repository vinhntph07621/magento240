<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 00:17
 */
namespace Omnyfy\StripeSubscription\Controller\Invoice\Payment;

class Failed extends \Omnyfy\StripeSubscription\Controller\AbstractAction
{
    protected $_queueTopic  = 'stripe_invoice_payment_failed';
}
 