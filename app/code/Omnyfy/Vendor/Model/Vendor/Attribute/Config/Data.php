<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-16
 * Time: 16:56
 */
namespace Omnyfy\Vendor\Model\Vendor\Attribute\Config;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;

class Data extends \Magento\Framework\Config\Data
{
    /**
     * Data constructor.
     *
     * @param \Omnyfy\Vendor\Model\Vendor\Attribute\Config\Reader $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     */
    public function __construct(
        \Omnyfy\Vendor\Model\Vendor\Attribute\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache)
    {
        parent::__construct($reader, $cache, 'vendor_attributes');
    }
}
 