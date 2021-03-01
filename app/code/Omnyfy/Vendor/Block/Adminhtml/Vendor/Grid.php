<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 4:57 PM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor;

use Magento\Backend\Model\Session as BackendSession;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $vendorCollectionFactory;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = [])
    {
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('vendorGrid');
        $this->setDefaultSort('vendor_id');
        //$this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

        $this->setFilterVisibility(false);

    }

    protected function _prepareCollection() {
        $collection = $this->vendorCollectionFactory->create();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}