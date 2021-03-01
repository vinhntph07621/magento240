<?php
/**
 * Project: Vendor.
 * User: jing
 * Date: 25/1/18
 * Time: 5:03 PM
 */
namespace Omnyfy\Vendor\Ui\Component\Listing\Column;


class InventoryActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['delete'] = [
                'href' => $this->urlBuilder->getUrl(
                    'omnyfy_vendor/inventory/delete',
                    ['id' => $item['entity_id'], 'location_id' => $item['location_id']]
                ),
                'label' => __('Remove'),
                'hidden' => false,
            ];
        }

        return $dataSource;
    }
}