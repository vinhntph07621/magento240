<?php
/**
 * Project: Rma per vendor
 * Author: seth
 * Date: 24/2/20
 * Time: 10:18 am
 **/

namespace Omnyfy\Rma\Plugin\Model\ResourceModel\Address;


class Collection
{
    /**
     * @var \Omnyfy\Rma\Helper\Data
     */
    protected $helper;

    /**
     * Collection constructor.
     * @param \Omnyfy\Rma\Helper\Data $helper
     */
    public function __construct(
        \Omnyfy\Rma\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }

    public function afteraddActiveFilter(\Mirasvit\Rma\Model\ResourceModel\Address\Collection $subject, $result) {
        if ($vendorId = $this->helper->getVendorId()) {
            $result->getSelect()
                ->where("vendor_id = $vendorId")
            ;
        }
        return $result;
    }
}