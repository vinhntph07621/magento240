<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-23
 * Time: 16:37
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Location\Form\Modifier;

abstract class AbstractModifier extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const FORM_NAME = 'omnyfy_vendor_location_form';
    const DATA_SOURCE_DEFAULT = 'location';
    const DATA_SCOPE_PRODUCT = 'data.location';

    /**
     * Name of default general panel
     */
    const DEFAULT_GENERAL_PANEL = 'location-details';
}
 