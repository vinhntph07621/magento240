<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 11/9/17
 * Time: 12:06 PM
 */
namespace Omnyfy\Vendor\Api\Data;

interface VendorAttributeInterface extends \Omnyfy\Vendor\Api\Data\EavAttributeInterface
{
    const ENTITY_TYPE_CODE = 'omnyfy_vendor_vendor';
    const CODE_STATUS = 'status';
}