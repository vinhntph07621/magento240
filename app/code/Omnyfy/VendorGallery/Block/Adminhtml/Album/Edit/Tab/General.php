<?php
namespace Omnyfy\VendorGallery\Block\Adminhtml\Album\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Omnyfy\Vendor\Model\LocationFactory;
use Omnyfy\Core\Model\Source\BooleanActive;
use Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory as VendorCollectionFactory;

class General extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    /**
     * @var BooleanActive
     */
    protected $booleanActive;

    /**
     * @var VendorCollectionFactory
     */
    protected $vendorCollectionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        LocationFactory $locationFactory,
        BooleanActive $booleanActive,
        VendorCollectionFactory $vendorCollectionFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->locationFactory = $locationFactory;
        $this->booleanActive = $booleanActive;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_album');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('album_');

        $fieldset = $form->addFieldset(
            'vendor_gallery_general',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'id']
            );
        }

        $vendorId = null;
        $isVendor = false;
        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (!empty($vendorInfo) && isset($vendorInfo['vendor_id'])) {
            $vendorId = $vendorInfo['vendor_id'];
            $fieldset->addField(
                'vendor_id',
                'hidden',
                ['name' => 'vendor_id']
            );
            $isVendor = true;
        } else {
            $vendorId = $model->getVendorId();
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'        => 'name',
                'label'    => __('Name'),
                'required'     => true
            ]
        );
        $wysiwygConfig = $this->wysiwygConfig->getConfig();
        $fieldset->addField(
            'description',
            'editor',
            [
                'name'        => 'description',
                'label'    => __('Description'),
                'required'     => true,
                'config'    => $wysiwygConfig
            ]
        );
        if (!$isVendor) {
            $fieldset->addField(
                'vendor_id',
                'select',
                [
                    'name' => 'vendor_id',
                    'label' => __('Vendor'),
                    'title' => __('Vendor'),
                    'values' => $this->getVendorsOptionArray(),
                    'required' => true,
                    'disabled' => $vendorId == null ? '': 'disabled'
                ]
            );
        }

        $locationOptions = $this->getLocationOptions($vendorId);
        $fieldset->addField(
            'location_ids',
            'multiselect',
            [
                'name' => 'location_ids[]',
                'label' => __('Location'),
                'title' => __('Location'),
                'values' => $locationOptions,
                'required' => true,
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $this->booleanActive->toOptionArray()
            ]
        );

        $data = $model->getData();
        $data['vendor_id'] = $vendorId;
        $data['location_ids'] = $model->getAllLocationIds();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General');
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

    /**
     * @param int $vendorId
     * @return array|mixed
     */
    protected function getLocationOptions($vendorId)
    {
        if ($vendorId == null) {
            return [];
        }
        return $this->locationFactory->create()->getOptions($vendorId);
    }

    protected function getVendorsOptionArray()
    {
        $vendorCollection = $this->vendorCollectionFactory->create();
        $optionArray = [['value' => null, 'label' => __("Please choose vendor for the album")]];
        foreach ($vendorCollection as $vendor) {
            $optionArray[] = [
                'value' => $vendor->getId(),
                'label' => $vendor->getName(),
            ];
        }
        return $optionArray;
    }
}
