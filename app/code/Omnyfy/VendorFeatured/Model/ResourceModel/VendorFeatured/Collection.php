<?php


namespace Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured;

use Omnyfy\VendorFeatured\Model\VendorFeaturedTag;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var VendorFeaturedTag
     */
    protected $_tag;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Omnyfy\VendorFeatured\Model\VendorFeaturedTag $tag,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        $this->_tag = $tag;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Omnyfy\VendorFeatured\Model\VendorFeatured',
            'Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured'
        );
    }

    public function joinTags()
    {
        $this->getSelect()->joinLeft(
            ["tag" => "omnyfy_vendorfeatured_vendor_featured_tag"],
            "tag.vendor_featured_id = main_table.vendor_featured_id",
            ["tag_id" => "tag.vendor_tag_id"]
        );

        $this->getSelect()->joinLeft(
            ["tn" => "omnyfy_vendorfeatured_vendor_tag"],
            "tag.vendor_tag_id = tn.vendor_tag_id",
            [ "tag_name" => "tn.name" ]
        );

        $this->getSelect()->group("main_table.vendor_id");

        return $this;
    }
}
