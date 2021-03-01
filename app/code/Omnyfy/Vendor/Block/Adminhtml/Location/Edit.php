<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 11:25 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $vendorResource;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->vendorResource = $vendorResource;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_location';
        $this->_blockGroup = 'Omnyfy_Vendor';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );

        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (empty($vendorInfo)) {
            $vendors = $this->vendorResource->loadVendorWithProfiles();
            $websites = $this->_storeManager->getWebsites();

            $this->_formScripts[] = "require(['jquery'],function(jQuery){";
            $this->_formScripts[] = 'var websiteOptions=' .
                \Zend_Json::encode($this->getAllVendorWebsiteOptions($vendors, $websites)) . ';';
            $this->_formScripts[] = "jQuery('#location_vendor_id').change(function(){";
            $this->_formScripts[] = "console.log(websiteOptions);";
            $this->_formScripts[] = "vendorId = jQuery('#location_vendor_id').val();";
            $this->_formScripts[] = "if (vendorId in websiteOptions){";
            $this->_formScripts[] = "o=websiteOptions[vendorId];var t=jQuery('#location_website_ids');t.empty();";
            $this->_formScripts[] = "for(var j=0;j<o.length;j++){t.append('<option value=\"'+o[j].value+'\">' +o[j].label+'</option>');}";
            $this->_formScripts[] = "}});";
            $this->_formScripts[] = "});";
        }

    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $location = $this->_coreRegistry->registry('current_omnyfy_vendor_location');
        if ($location->getId()) {
            return __("Edit Location '%1'", $this->escapeHtml($location->getLocationName()));
        } else {
            return __('New Location');
        }
    }

    protected function _prepareLayout()
    {
        $location = $this->_coreRegistry->registry('current_omnyfy_vendor_location');
        if (!empty($location) && $location->getId()) {
            $title = __("Edit Location '%1'", $this->escapeHtml($location->getLocationName()));

        } else {
            $title = __('New Location');
        }

        // check if the block exists before trying to set page title
        if ($this->getLayout()->getBlock('page.title')) {
            $this->getLayout()->getBlock('page.title')->setPageTitle($title);
        }

        return parent::_prepareLayout();
    }

    protected function getAllVendorWebsiteOptions($vendors, $websites)
    {
        $result = [];
        foreach($vendors as $vendorId => $vendor) {
            $webOptions = [];
            foreach($vendor['website_ids'] as $websiteId ){
                $website = $websites[$websiteId];
                $webOptions[] = [
                    'value' => $websiteId,
                    'label' => $website->getName()
                ];
            }
            $result[$vendorId] = $webOptions;
        }
        return $result;
    }
}
