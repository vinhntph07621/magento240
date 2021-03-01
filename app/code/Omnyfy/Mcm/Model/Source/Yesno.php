<?php
namespace Omnyfy\Mcm\Model\Source;

class Yesno implements \Magento\Framework\Option\ArrayInterface
{
    protected $shippingHelper;

    public function __construct(
        \Omnyfy\Vendor\Helper\Shipping $shippingHelper
    ) {
        $this->shippingHelper = $shippingHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [
            [
                'value' => 0,
                'label' => __('No')
            ]
        ];

        if ($this->shippingHelper->getCalculateShippingBy() == 'overall_cart') {
            $optionArray[] = [
                'value' => 1,
                'label' => __('Yes')
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $optionArray =[
            0 => __('No')
        ];
        if ($this->shippingHelper->getCalculateShippingBy() == 'overall_cart') {
            $optionArray[] = [
                1 => __('Yes')
            ];
        }

        return $optionArray;
    }
}
