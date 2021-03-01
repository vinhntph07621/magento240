<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 11:33 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $vendorResource;

    protected $regionFactory;

    protected $timezone;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Locale\Timezone $timezone,
        array $data = [])
    {
        $this->vendorResource = $vendorResource;

        $this->regionFactory = $regionFactory;

        $this->timezone = $timezone;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Location Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Location Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareLayout()
    {
        $model = $this->_coreRegistry->registry('current_omnyfy_vendor_location');
        $this->pageConfig->getTitle()->set(__('Add Location'));
        if ($model->getId()) {
            $this->pageConfig->getTitle()->set(__('Edit Location'));
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_omnyfy_vendor_location');
        //TODO: if current user is vendor admin, set vendor id as hidden field, otherwise show a vendor drop-down
        $vendors = $this->vendorResource->loadVendorWithProfiles();

        $vendorInfo = $this->_backendSession->getVendorInfo();

        // load all website ids
        $websites = $this->_storeManager->getWebsites();
        $websiteOpts = [];
        foreach($websites as $id => $website) {
            if (empty($vendorInfo) || (isset($vendorInfo['website_ids']) && in_array($id, $vendorInfo['website_ids'])))
            $websiteOpts[] = ['value' => $id, 'label' => $website->getName()];
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('location_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Location Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'location[id]']);
            $fieldset->addField('entity_id', 'hidden', ['name' => 'location[entity_id]']);
            $model->setData('id', $model->getId());
            // set location website ids in to model
            $websiteIds = $this->vendorResource->getWebsiteIdsByLocationId($model->getId());
            $model->setData('website_ids', $websiteIds);
            $model->setData(
                'address_full',
                $model->getAddress() . ', ' . $model->getSuburb().', '.$model->getRegion(). ', '.$model->getCountry()
            );
        }

        $fieldset->addField(
            'location_name',
            'text',
            [
                'name' => 'location[location_name]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Location Name'),
                'title' => __('Location Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'address_full',
            'text',
            [
                'name' => 'location[address_full]',
                'label' => __('Location Address'), 'title' => __('Location Address'), 'required' => true]
        );

        $fieldset->addField(
            'address',
            'hidden',
            [
                'name' => 'location[address]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'after_element_js' => '<script>google_map_initialize();</script>'
            ]
        );

        $fieldset->addField(
            'latitude',
            'text',
            [
                'name' => 'location[latitude]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'longitude',
            'text',
            [
                'name' => 'location[longitude]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => true
            ]
        );

        /*$fieldset->addField(
            'timezone',
            'select',
            [
                'name' => 'location[timezone]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Time Zone'),
                'title' => __('Time Zone'),
                'required' => true,
                'values' => $this->timezone->toOptionArray()
            ]
        );*/


        $fieldset->addField(
            'suburb',
            'hidden',
            [
                'name' => 'location[suburb]',
                'data-form-part' => 'omnyfy_vendor_location_form',
            ]
        );
        $fieldset->addField(
            'region_id',
            'hidden',
            [
                'name' => 'location[region_id]',
                'data-form-part' => 'omnyfy_vendor_location_form',
            ]
        );
        $fieldset->addField(
            'region',
            'hidden',
            [
                'name' => 'location[region]',
                'data-form-part' => 'omnyfy_vendor_location_form',
            ]
        );
        $fieldset->addField(
            'country',
            'hidden',
            [
                'name' => 'location[country]',
                'data-form-part' => 'omnyfy_vendor_location_form',
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'location[postcode]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Postcode'),
                'title' => __('Postcode'),
                'required' => true
            ]
        );

        if (empty($vendorInfo)) {
            $fieldset->addField(
                'vendor_id',
                'select',
                [
                    'name' => 'location[vendor_id]',
                    'data-form-part' => 'omnyfy_vendor_location_form',
                    'label' => __('Vendor'),
                    'title' => __('Vendor'),
                    'values' => $this->convertVendorOptions($vendors),
                    'required' => true,
                ]
            );
        }
        else {
            $fieldset->addField(
                'vendor_id',
                'hidden',
                [
                    'name' => 'location[vendor_id]',
                    'data-form-part' => 'omnyfy_vendor_location_form',
                    'value' => $vendorInfo['vendor_id']
                ]
            );
        }
        $fieldset->addField(
            'website_ids',
            'multiselect',
            [
                'name' => 'location[website_ids][]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Marketplaces'),
                'title' => __('Marketplaces'),
                'required' => true,
                'values' => $websiteOpts
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'location[description]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Description'),
                'title' => __('Description')
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'location[status]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label'=> __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => ['0' => __('Inactive'), '1' => __('Active')],
            ]
        );

        $fieldset->addField(
            'is_warehouse',
            'select',
            [
                'name' => 'location[is_warehouse]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Is Warehouse'),
                'title' => __('Is Warehouse'),
                'required' => true,
                'values' => [0 => __('No'), 1 => __('Yes')]
            ]
        );

        $fieldset->addField(
            'location_contact_name',
            'text',
            [
                'name' => 'location[location_contact_name]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Location Contact Name'),
                'title' => __('Location Contact Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'location_contact_phone',
            'text',
            [
                'name' => 'location[location_contact_phone]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Location Contact Phone'),
                'title' => __('Location Contact Phone'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'location_contact_email',
            'text',
            [
                'name' => 'location[location_contact_email]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Location Contact Email'),
                'title' => __('Location Contact Email'),
                'class' => 'required-entry validate-email',
                'required' => true
            ]
        );

        $fieldset->addField(
            'location_company_name',
            'text',
            [
                'name' => 'location[location_company_name]',
                'data-form-part' => 'omnyfy_vendor_location_form',
                'label' => __('Location Company Name'),
                'title' => __('Location Company Name'),
                'required' => true
            ]
        );

        if ($this->_scopeConfig->isSetFlag(\Omnyfy\Vendor\Model\Config::XML_PATH_SHOW_LOC_PRIORITY)) {
            $fieldset->addField(
                'priority',
                'text',
                [
                    'name' => 'location[priority]',
                    'data-form-part' => 'omnyfy_vendor_location_form',
                    'label' => __('Priority'),
                    'title' => __('Priority'),
                    'required' => true,
                    'note' => __('Lower number higher priority')
                ]
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function convertVendorOptions($vendors)
    {
        $result = [];
        foreach($vendors as $vendorId => $vendor) {
            $result[] = [
                'value' => $vendorId,
                'label' => __($vendor['name'])
            ];
        }
        return $result;
    }

    public function isAjaxLoaded()
    {
        return false;
    }
}
