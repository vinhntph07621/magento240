<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-15
 * Time: 12:40
 */
namespace Omnyfy\StripeSubscription\Model\Resource\WebHookContent;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Omnyfy\StripeSubscription\Model\WebHookContent', 'Omnyfy\StripeSubscription\Model\Resource\WebHookContent');
    }
}
 