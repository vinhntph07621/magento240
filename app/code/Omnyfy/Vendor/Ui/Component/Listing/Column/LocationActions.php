<?php
/**
 * Project: magento2.
 * User: jing
 * Date: 10/11/17
 * Time: 11:16 AM
 */
namespace Omnyfy\Vendor\Ui\Component\Listing\Column;


class LocationActions extends \Magento\Ui\Component\Listing\Columns\Column
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
            $item[$this->getData('name')]['edit'] = [
                'href' => $this->urlBuilder->getUrl(
                    'omnyfy_vendor/location/edit',
                    ['id' => $item['entity_id']]
                ),
                'label' => __('Edit'),
                'hidden' => false,
            ];

            $item[$this->getData('name')]['stock'] = [
                'href' => $this->urlBuilder->getUrl(
                    'omnyfy_vendor/location/stock',
                    ['id' => $item['entity_id']]
                ),
                'label' => __('Inventory'),
                'hidden' => false,
            ];

        }

        return $dataSource;
    }
}