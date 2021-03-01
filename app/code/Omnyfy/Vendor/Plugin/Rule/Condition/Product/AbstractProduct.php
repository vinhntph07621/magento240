<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 30/8/18
 * Time: 10:37 AM
 */
namespace Omnyfy\Vendor\Plugin\Rule\Condition\Product;

class AbstractProduct
{
    protected $_assetRepo;

    protected $_backendData;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Backend\Helper\Data $backendData
    )
    {
        $this->_assetRepo = $assetRepo;
        $this->_backendData = $backendData;
    }

    public function afterLoadAttributeOptions(
        \Magento\Rule\Model\Condition\Product\AbstractProduct $subject,
        $result
    ) {
        if (is_a($subject, 'Magento\SalesRule\Model\Rule\Condition\Product')) {
            $attributes = $result->getAttributeOption();
            $attributes['vendor_id'] = __('Vendor');
            $attributes['location_id'] = __('Location');
            asort($attributes);
            $result->setAttributeOption($attributes);
        }

        return $result;
    }

    public function afterGetValueElementChooserUrl(
        \Magento\Rule\Model\Condition\Product\AbstractProduct $subject,
        $result
    )
    {
        $attributeCode = $subject->getData('attribute');
        if ('vendor_id' == $attributeCode || 'location_id' == $attributeCode) {
            $url = 'omnyfy_vendor/promo_widget/chooser/attribute/' . $attributeCode;
            if ($subject->getJsFormObject()) {
                $url .= '/form/' . $subject->getJsFormObject();
            }
            $result = $this->_backendData->getUrl($url);
        }
        return $result;
    }

    public function afterGetValueAfterElementHtml(
        \Magento\Rule\Model\Condition\Product\AbstractProduct $subject,
        $result
    )
    {
        $attributeCode = $subject->getData('attribute');
        if ('vendor_id' == $attributeCode || 'location_id' == $attributeCode) {
            $image = $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif');
            $result = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
                $image .
                '" alt="" class="v-middle rule-chooser-trigger" title="' .
                __(
                    'Open Chooser'
                ) . '" /></a>';
        }
        return $result;
    }

    public function afterGetExplicitApply(
        \Magento\Rule\Model\Condition\Product\AbstractProduct $subject,
        $result
    )
    {
        $attributeCode = $subject->getData('attribute');
        if ('vendor_id' == $attributeCode || 'location_id' == $attributeCode) {
            return true;
        }
        return $result;
    }
}