<?php
namespace Omnyfy\VendorGallery\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Omnyfy\Vendor\Model\Resource\Location\CollectionFactory as LocationCollectionFactory;

/**
 * Class Store
 */
class Location extends Column
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var LocationCollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        LocationCollectionFactory $locationCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->locationCollectionFactory = $locationCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        $locationCollection = $this->locationCollectionFactory->create();
        $locationCollection->addFieldToFilter('entity_id', ['eq' => $item['locations']]);
        foreach ($locationCollection->getItems() as $location) {
            $content .= $this->escaper->escapeHtml($location->getData('location_name')) . "<br/>";
        }

        return $content;
    }
}
