<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 13/7/18
 * Time: 11:30 AM
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Product\Form\Modifier;

class Vendors extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    protected $_locator;

    protected $_vendorResource;

    protected $_backendSession;

    protected $_vendorCollectionFactory;

    protected $_options;

    protected $_vendorConfig;

    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Backend\Model\Session $backendSession,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Model\Config $vendorConfig
    )
    {
        $this->_locator = $locator;
        $this->_vendorResource = $vendorResource;
        $this->_backendSession = $backendSession;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_vendorConfig = $vendorConfig;
    }

    public function modifyData(array $data)
    {
        $productId = $this->_locator->getProduct()->getId();
        $vendorIds = $this->_vendorResource->getVendorIdArrayByProductId($productId);
        $data[$productId]['product']['vendor_ids'] = $vendorIds;
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (empty($vendorInfo) && $this->_vendorConfig->isAdminAcrossVendor()) {
            $meta['vendor_info'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'fieldset',
                            'label' => __('Vendor Information'),
                            'collapsible' => true,
                            'dataScope' => 'data.product',
                            'sortOrder' => 200,
                        ]
                    ]
                ],
                'children' => [
                    'vendor_ids' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => 'select',
                                    'componentType' => 'field',
                                    'component' => 'Magento_Ui/js/form/element/ui-select',
                                    'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                    'filterOptions' => true,
                                    'chipsEnabled' => true,
                                    'disableLabel' => true,
                                    'showCheckbox' => false,
                                    'multiple' => $this->_vendorConfig->isVendorShareProducts(),
                                    'source' => 'product',
                                    'options' => $this->getVendors(),
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        return $meta;
    }

    protected function getVendors()
    {
        if (empty($this->_options)) {
            $collection = $this->_vendorCollectionFactory->create();
            $arr = [
                ['label' => 'Please Select', 'value' => '']
            ];
            foreach($collection as $vendor) {
                $arr[] = ['value' => $vendor->getId(), 'label' => $vendor->getName()];
            }
            $this->_options = $arr;
        }
        return $this->_options;
    }
}