<?php 
namespace Omnyfy\VendorSignUp\Model\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_NEW = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;


    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach($this->toValuesArray() as $key => $value) {
            $result[] = ['value' => $key, 'label' => $value];
        }
        return $result;
    }

    public function toValuesArray()
    {
        return [
            self::STATUS_NEW => __('New'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_REJECTED => __('Rejected'),
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}