<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-18
 * Time: 15:00
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Flat;

use Magento\Store\Model\ScopeInterface;

class State
{
    const INDEXER_ID = 'omnyfy_vendor_vendor_flat';

    const INDEXER_ENABLED_XML_PATH ='omnyfy_vendor/vendor/flat_vendor';

    protected $_vendorFlatIndexerHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var bool
     */
    protected $isAvailable;

    /** @var \Magento\Framework\Indexer\IndexerRegistry */
    protected $indexerRegistry;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $flatIndexerHelper,
        $isAvailable = false
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->indexerRegistry = $indexerRegistry;
        $this->isAvailable = $isAvailable;
        $this->_vendorFlatIndexerHelper = $flatIndexerHelper;
    }

    public function getFlatIndexerHelper()
    {
        return $this->_vendorFlatIndexerHelper;
    }

    /**
     * Check if Flat Index is enabled
     *
     * @return bool
     */
    public function isFlatEnabled()
    {
        return $this->scopeConfig->isSetFlag(static::INDEXER_ENABLED_XML_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if Flat Index is available for use
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->isAvailable && $this->isFlatEnabled()
            && $this->indexerRegistry->get(static::INDEXER_ID)->isValid();
    }
}
 