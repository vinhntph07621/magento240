<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 10/9/19
 * Time: 3:59 pm
 */
namespace Omnyfy\VendorSubscription\Block\Adminhtml\Vendor\Edit\Tab\Subscription;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Detail extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'vendor/edit/subscription.phtml';

    protected $subscriptionStatus;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus $subscriptionStatus,
        array $data = []
    )
    {
        $this->subscriptionStatus = $subscriptionStatus;
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getFieldValue($field)
    {
        $subscription = $this->getSubscription();
        if (empty($subscription)) {
            return '';
        }

        switch($field) {
            case '':
                break;
            default:

        }
    }
}
 