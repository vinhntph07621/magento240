<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-24
 * Time: 10:41
 */

/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-23
 * Time: 16:31
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Location\Form;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $pool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Ui\DataProvider\Modifier\PoolInterface $pool,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $collectionFactory,
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

 