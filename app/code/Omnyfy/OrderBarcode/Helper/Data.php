<?php
/**
 * Project: Order Barcode
 * User: jing
 * Date: 19/11/19
 * Time: 3:16 pm
 */
namespace Omnyfy\OrderBarcode\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_generator;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Picqer\Barcode\BarcodeGeneratorPNG $generator
    ) {
        $this->_generator = $generator;
        parent::__construct($context);
    }

    public function toBarcode($string)
    {
        return $this->_generator->getBarcode(
            $string,
            \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128_B,
            2,
            60
        );
    }
}

 