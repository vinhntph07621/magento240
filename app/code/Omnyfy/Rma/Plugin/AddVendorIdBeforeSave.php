<?php
/**
 * Project:
 * Author: seth
 * Date: 18/2/20
 * Time: 5:53 pm
 **/

namespace Omnyfy\Rma\Plugin;


class AddVendorIdBeforeSave
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * AddVendorIdBeforeSave constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory
    )
    {
        $this->itemCollectionFactory = $itemCollectionFactory;
    }

    /**
     * Add Vendor Id.
     *
     * @param \Mirasvit\Rma\Controller\Rma\Save $subject
     * @param callable $process
     * @return mixed
     */
    public function aroundExecute(\Mirasvit\Rma\Controller\Rma\Save $subject, callable $process)
    {
        $data = $subject->getRequest()->getParams();

        if (!empty($data)) {
            if (isset($data['items'])) {
                $orderItemCollection = $this->itemCollectionFactory->create();
                $orderItemCollection->addFieldToFilter('item_id', ['in' => array_keys($data['items'])]);

                $newItems = [];
                $vendorIds = $orderItemCollection->getColumnValues('vendor_id');
                if ($orderItemCollection->getSize() && count($vendorIds) == count($data['items'])) {
                    $index = 0;
                    foreach ($data['items'] as $key => $value) {
                        error_log(print_r($value, true));
                        if (isset($value['is_return'])) {
                            if ($value['is_return'] == 1) {
                                $value['vendor_id'] = $vendorIds[$index];
                                $newItems[$key] = $value;
                            }
                        }
                        $index++;
                    }
                }
                $subject->getRequest()->setParam('items', $newItems);
            }
        }

        return $process();
    }

    /**
     * @param \Mirasvit\Rma\Controller\Rma\Save $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Mirasvit\Rma\Controller\Rma\Save $subject, $result) {
        return $result->setPath('*/*/list');
    }
}