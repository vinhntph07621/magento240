<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-16
 * Time: 15:28
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Vendor\Form\Modifier;

abstract class AbstractModifier extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const FORM_NAME = 'omnyfy_vendor_vendor_store_form';
    const DATA_SOURCE_DEFAULT = 'vendor';
    const DATA_SCOPE_PRODUCT = 'data.vendor';

    /**
     * Name of default general panel
     */
    const DEFAULT_GENERAL_PANEL = 'vendor-details';
}