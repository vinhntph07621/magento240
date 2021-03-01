<?php

namespace Omnyfy\Mcm\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Omnyfy\Vendor\Ui\Component\Listing\Column\VendorActions as OldVendorActions;

class VendorActions extends OldVendorActions {

    const URL_PATH_EDIT = 'omnyfy_vendor/vendor/edit';

    protected $urlBuilder;

    public function __construct(
    ContextInterface $context, UiComponentFactory $uiComponentFactory, UrlInterface $urlBuilder, array $components = [], array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $urlBuilder, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        //parent::prepareDataSource($dataSource);

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['vendor_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_EDIT, ['id' => $item['vendor_id']]),
                        'label' => __('Edit')
                    ];

                    $item[$name]['vendor_earning'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_EDIT, ['vendor_id' => $item['vendor_id']]),
                        'label' => __('Vendor Earnings')
                    ];
                }
            }
        }
        return $dataSource;
    }

}
