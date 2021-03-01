<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/9/17
 * Time: 10:17 AM
 */
namespace Omnyfy\Vendor\Api\Data;


interface LocationAttributeInterface extends \Omnyfy\Vendor\Api\Data\EavAttributeInterface
{
    const ENTITY_TYPE_CODE = 'omnyfy_vendor_location';
    const CODE_STATUS = 'status';
    const CODE_IS_WAREHOUSE = 'is_warehouse';
}
