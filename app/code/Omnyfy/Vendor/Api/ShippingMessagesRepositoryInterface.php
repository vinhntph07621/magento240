<?php

namespace Omnyfy\Vendor\Api;

interface ShippingMessagesRepositoryInterface
{
    /**
     *
     * @api
     * @param string $type
     * @return \Omnyfy\Core\Api\Json
     */
    public function getShippingConfig($type);

    /**
     * @api
     * @param int $id
     * @return \Omnyfy\Core\Api\Json
     */
    public function getVendorThreshold($id);

}
