<?php
/**
 * Project: Strip API
 * User: jing
 * Date: 2019-07-18
 * Time: 14:07
 */
use Magento\Framework\Component\ComponentRegistrar;
ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Omnyfy_StripeSubscription',
    __DIR__
);

 