<?php

namespace Vinh\CustomPayment\Model;

class CustomPayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'custompayment';
}
