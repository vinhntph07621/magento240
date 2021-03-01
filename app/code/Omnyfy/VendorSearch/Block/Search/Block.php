<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 19/07/2019
 * Time: 1:40 PM
 */

namespace Omnyfy\VendorSearch\Block\Search;


use Magento\Framework\View\Element\Template;

class Block extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory
     */
    protected $_vendorTypeCollectionFactory;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory $vendorTypeCollectionFactory,
        Template\Context $context,
        array $data = [])
    {
        $this->_vendorTypeCollectionFactory = $vendorTypeCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getVendorTypes(){
        /** @var \Omnyfy\Vendor\Model\Resource\VendorType\Collection $collection */
        $collection = $this->_vendorTypeCollectionFactory->create();
        $collection->addFieldToFilter('status',['eq' => 1]);
        return $collection;
    }
}