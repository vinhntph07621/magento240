<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 10/9/19
 * Time: 3:25 pm
 */
namespace Omnyfy\VendorSubscription\Block\Adminhtml\Vendor\Renderer;

class Plan extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'vendor/edit/plan.phtml';

    protected $_coreRegistry;

    protected $dataHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorSubscription\Helper\Data $dataHelper,
        array $data = [])
    {
        $this->_coreRegistry = $registry;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getSubscription()
    {
        $subscription = $this->getData('subscription');
        $subscription = empty($subscription) ? $this->_coreRegistry->registry('current_omnyfy_subscription_subscription') : $subscription;
        return empty($subscription) ? false : $subscription;
    }

    public function getVendor()
    {
        $vendor = $this->getData('vendor');
        $vendor = empty($vendor) ? $this->_coreRegistry->registry('current_omnyfy_vendor_vendor') : $vendor;
        return empty($vendor) ? false : $vendor;
    }

    public function getPlanName()
    {
        $sub = $this->getSubscription();
        if (empty($sub)) {
            return '';
        }

        return $sub->getPlanName();
    }

    public function getButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    protected function _prepareLayout()
    {
        $sub = $this->getSubscription();
        $params = [];
        if (!empty($sub)) {
            $params['id'] = $sub->getId();
        }

        if (empty($sub->getGatewayId())) {
            return parent::_prepareLayout();
        }

        //check if current plan is the only option for current vendor type
        $allowedPlans = $this->dataHelper->getUpdatePlans($sub->getVendorTypeId(), $sub->getPlanId());
        //If so, do not show the button.
        if (empty($allowedPlans)) {
            return parent::_prepareLayout();
        }

        //DO NOT show button when there's processing update
        $update = $this->dataHelper->loadProcessingUpdate($sub->getId());
        if (!empty($update)) {
            return parent::_prepareLayout();
        }

        //DO NOT show button when status is deleted
        if (\Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus::STATUS_DELETED == $sub->getStatus()) {
            return parent::_prepareLayout();
        }

        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Change Plan'),
                'onclick' => 'location.href=\'' . $this->getUrl('omnyfy_subscription/subscription_update/edit', $params) . '\'',
                'class' => 'primary'
            ]
        );
        $button->setName('update_plan_button');
        $this->setChild('update_button', $button);
        return parent::_prepareLayout();
    }
}
 