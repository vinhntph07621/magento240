<?php
/**
 * Project: Vendors.
 * User: jing
 * Date: 3/11/18
 * Time: 2:09 PM
 */
namespace Omnyfy\Vendor\Plugin\SalesRule;

class DataProvider
{
    protected $_backendSession;

    protected $_vendorCollectionFactory;

    protected $_options;

    protected $_vendorConfig;

    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Model\Config $vendorConfig
    )
    {
        $this->_backendSession = $backendSession;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_vendorConfig = $vendorConfig;
    }

    public function aroundGetMeta($subject, callable $process)
    {
        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (!empty($vendorInfo) || !$this->_vendorConfig->isAdminAcrossVendor()) {
            return $process();
        }
        $meta = $process();

        if (!empty($vendorInfo)) {
            $meta['rule_information']['children']['vendor_id'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Vendor'),
                            'formElement' => 'hidden',
                            'componentType' => 'field',
                            'value' => $vendorInfo['vendor_id'],
                        ]
                    ]
                ]
            ];
        } else {

            $meta['rule_information']['children']['vendor_id'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Vendor'),
                            'formElement' => 'select',
                            'componentType' => 'field',
                            'component' => 'Magento_Ui/js/form/element/ui-select',
                            'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                            'filterOptions' => true,
                            'chipsEnabled' => false,
                            'disableLabel' => true,
                            'showCheckbox' => false,
                            'multiple' => false,
                            'options' => $this->getVendors(),
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
