<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 14/02/2019
 * Time: 11:58 AM
 */

namespace Omnyfy\Vendor\Ui\Component\Listing\Column;


class NumberOfOrders extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $_orderItemCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components = []
     * @param array $data = []
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Psr\Log\LoggerInterface $logger,
        array $components = [],
        array $data = []
    ){
        $this->_logger = $logger;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if ($items['sku']) {
                    $items['num_of_orders'] = $this->getNumOrdersPerSku($items['sku'], $items['vendor_id'], $items['location_id']);
                } else {
                    $items['num_of_orders'] = 0;
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $sku
     * @return int
     */
    public function getNumOrdersPerSku($sku, $vendorId, $locationId){
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderItemCollection */
        $orderItemCollection = $this->_orderItemCollectionFactory->create();
        $orderItemCollection->addFieldToSelect('order_id');
        $orderItemCollection->addFieldToFilter('sku',['eq'=>$sku]);
        $orderItemCollection->addFieldToFilter('vendor_id',['eq'=>$vendorId]);
        $orderItemCollection->addFieldToFilter('location_id',['eq'=>$locationId]);
        $orderItemCollection->distinct(true);
        $orderItemCollection->load();

        $this->_logger->debug("***********************");
        $this->_logger->debug($orderItemCollection->getSelectSql());

        return $orderItemCollection->count();
    }
}