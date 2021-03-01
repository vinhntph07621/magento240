<?php
/**
 * Project: .
 * User: Abhay
 * Date: 06/02/18
 * Time: : 10:20 AM
 */
namespace Omnyfy\VendorSignUp\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class Environment implements ArrayInterface
{
    const ENV_PRODUCTION = 'production';
    const ENV_SANDBOX = 'prelive';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENV_SANDBOX,
                'label' => 'Pre Live',
            ],
            [
                'value' => self::ENV_PRODUCTION,
                'label' => 'Production'
            ]
        ];
    }
}