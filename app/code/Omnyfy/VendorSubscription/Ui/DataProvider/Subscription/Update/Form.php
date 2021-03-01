<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 10/9/19
 * Time: 5:51 pm
 */
namespace Omnyfy\VendorSubscription\Ui\DataProvider\Subscription\Update;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Omnyfy\VendorSubscription\Model\Resource\Subscription\CollectionFactory;

class Form extends AbstractDataProvider
{
    protected $pool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = [])
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
    }

    public function getData()
    {
        /** @var ModifierInterface $modifier */
        foreach($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
 