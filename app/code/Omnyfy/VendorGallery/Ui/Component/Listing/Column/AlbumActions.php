<?php
namespace Omnyfy\VendorGallery\Ui\Component\Listing\Column;

class AlbumActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session $backendSession,
        array $components = [],
        array $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        $this->backendSession = $backendSession;
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
                    'vendor_gallery/album/edit',
                    ['id' => $item['entity_id']]
                ),
                'label' => __('Edit'),
                'hidden' => false,
            ];
        }

        return $dataSource;
    }
}
