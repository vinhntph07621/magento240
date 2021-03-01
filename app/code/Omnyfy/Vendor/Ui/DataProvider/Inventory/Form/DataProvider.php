<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 30/1/18
 * Time: 11:56 AM
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Inventory\Form;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    private $pool;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $collectionFactory,
        \Magento\Ui\DataProvider\Modifier\PoolInterface $pool,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}