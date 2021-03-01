<?php
namespace Omnyfy\VendorSearch\Model\Config\Source;

class Columns implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (null == $this->_options) {
            $result = [];
            $columns = ['2','3','4','5','6','7','8'];

            foreach($columns as $column)
            {
                $result[] = [
                    "value" => $column,
                    "label" => $column
                ];
            }
            $this->_options = $result;
        }

        return $this->_options;
    }
}
