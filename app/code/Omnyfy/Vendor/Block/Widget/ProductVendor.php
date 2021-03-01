<?php

namespace Omnyfy\Vendor\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Omnyfy\Vendor\Block\Vendor\Brand;

class ProductVendor extends Brand implements BlockInterface
{

    protected $_template = "widget/productvendor.phtml";

}
