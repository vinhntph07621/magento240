<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 21/8/18
 * Time: 2:06 PM
 */
namespace Omnyfy\Vendor\Api\Data;

interface ProfileInterface {
    /**
     * @return array|null
     */
    public function loadUpdates();
}