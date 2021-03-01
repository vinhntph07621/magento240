<?php
namespace Smartwave\Post\Block;

class ListPost extends \Magento\Framework\View\Element\Template {

    protected $collection;

    public function __construct(
        \Smartwave\Post\Model\ResourceModel\Post\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->collection = $collectionFactory;
        parent::__construct($context, $data);
    }

    public function getCollectionPost(){
        return $this->collection->create();
    }
}
