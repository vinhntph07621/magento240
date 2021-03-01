<?php

namespace Omnyfy\Cms\Ui\DataProvider\Article\Form;

use Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class ArticleDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Article\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $articleCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $articleCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Omnyfy\Cms\Model\Config\Source\ToolTemplate $toolTemplateOption,
        \Omnyfy\Cms\Model\ResourceModel\Article $articleResource,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $articleCollectionFactory->create();
        $this->_toolTemplateOption = $toolTemplateOption;
        $this->dataPersistor = $dataPersistor;
        $this->articleResource = $articleResource;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $article \Omnyfy\Cms\Model\Article */
        foreach ($items as $article) {
            $data = $article->getData();

            /* Prepare Featured Image */
            $map = [
                'featured_img' => 'getFeaturedImage',
                'og_img' => 'getOgImage'
            ];
            foreach ($map as $key => $method) {
                if (isset($data[$key])) {
                    $name = $data[$key];
                    unset($data[$key]);
                    $data[$key][0] = [
                        'name' => $name,
                        'url' => $article->$method(),
                    ];
                }
            }

            $data['data'] = ['links' => []];

            /* Prepare related articles */
            $collection = $article->getRelatedArticles();
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                ];
            }
            $data['data']['links']['article'] = $items;

            /* Prepare related products */
            $collection = $article->getRelatedProducts()->addAttributeToSelect('name');
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];
            }
            $data['data']['links']['product'] = $items;
            
            /* Prepare related services */
            $collection = $article->getRelatedServices();
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'vendor_name' => $item->getData('name'), 
//                    'status' => $item->getStatus() == 1 ? 'Enabled' : 'Disabled' ,
                    'location_name' => $item->getLocationName(),
                ];
            }
//            \Magento\Framework\App\ObjectManager::getInstance()
//                ->get('Psr\Log\LoggerInterface')->debug('test: '.print_r($items,true));
            $data['data']['links']['service'] = $items;
            
            /* Prepare related tool */
            $collection = $article->getRelatedTools();
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'title' => $item->getTitle(), 
//                    'status' => $item->getStatus() == 1 ? 'Enabled' : 'Disabled' ,
                    'type' => $this->getTypeValue($item->getType()),
                ];
            }
            $data['data']['links']['tool'] = $items;
            $data['service_category'] = $this->articleResource->lookupServiceCategoryIds($article->getId());
            /* Set data */
            $this->loadedData[$article->getId()] = $data;
        }

        return $this->loadedData;
    }
	
	public function getTypeValue($field) {
        $fieldLabels = $this->_toolTemplateOption->toArray();
        return $fieldLabels[$field];
    }
}
