<?php

namespace Omnyfy\Mcm\Model\Source;

class VendorBankAccount implements \Magento\Framework\Option\ArrayInterface {

    protected $collectionFactory;

    public function __construct(
    \Omnyfy\Mcm\Model\ResourceModel\VendorBankAccount\CollectionFactory $collectionFactory, \Magento\Framework\App\RequestInterface $request
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->_request = $request;
    }

    /**
     * Retrieve Vendor bank account type options array.
     *
     * @return array
     */
    public function toOptionArray() {
        $arr = $this->_toArray();
        $ret = [];

        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    public function _toArray() {
        $collection = $this->collectionFactory->create();
        $vendorId = $this->_request->getParam('vendor_id');
        $collection = $collection->addFieldToFilter('vendor_id', $vendorId);
        $bankAccList = array();
        foreach ($collection as $bankAcc) {
            $bankAccList[$bankAcc->getId()] = __($bankAcc->getAccountName());
        }
        return $bankAccList;
    }

}
