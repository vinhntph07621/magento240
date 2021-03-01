<?php

namespace Omnyfy\Mcm\Model\Source;

class VendorBankAccountType implements \Magento\Framework\Option\ArrayInterface {

    protected $collectionFactory;

    public function __construct(
    \Omnyfy\Mcm\Model\ResourceModel\VendorBankAccountType\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
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
        $bankAccTypeList = array();
        foreach ($collection as $bankAcc) {
            $bankAccList[$bankAcc->getId()] = __($bankAcc->getAccountType());
        }
        return $bankAccList;
    }

}
