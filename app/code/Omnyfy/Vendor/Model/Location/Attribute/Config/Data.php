<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 17:08
 */
namespace Omnyfy\Vendor\Model\Location\Attribute\Config;

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
        \Omnyfy\Vendor\Model\Location\Attribute\Config\Reader $reader,
        \Magento\Framework\Config\CacheInterface $cache)
    {
        parent::__construct($reader, $cache, 'location_attributes');
    }
}
 