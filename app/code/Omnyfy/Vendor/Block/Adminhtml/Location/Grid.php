<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 9:42 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location;

use Magento\Backend\Model\Session as BackendSession;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $locationCollectionFactory;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = [])
    {
        $this->locationCollectionFactory = $locationCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('locationGrid');
        $this->setDefaultSort('entity_id');
        //$this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

        $this->setFilterVisibility(false);

    }

    protected function _prepareCollection() {
        $collection = $this->locationCollectionFactory->create();

        $collection->joinVendorInfo();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}