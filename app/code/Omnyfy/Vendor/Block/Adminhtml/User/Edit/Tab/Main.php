<?php
namespace Omnyfy\Vendor\Block\Adminhtml\User\Edit\Tab;

use Magento\Backend\Block\Widget\Form;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\OptionInterface;

class Main extends \Magento\User\Block\User\Edit\Tab\Main
{
    protected $_systemStore;

    protected $_vendorCollectionFactory;

    protected $_resourceConnection;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    protected $_userHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Store\Model\System\Store $systemStore,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Omnyfy\Vendor\Helper\User $userHelper,
        array $data = [],
        OptionInterface $deployedLocales = null
    ) {
        parent::__construct($context, $registry, $formFactory, $authSession, $localeLists, $data, null);
        $this->_systemStore = $systemStore;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_resourceConnection = $resourceConnection;
        $this->_adminSession = $adminSession;
        $this->_userHelper = $userHelper;
    }

    /**
     * Prepare form fields
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $model = $this->_coreRegistry->registry('permissions_user');
        $baseFieldset = $form->getElement('base_fieldset');

        $roleData = $this->_adminSession->getUser()->getRole();

        $canEditRole = $this->_userHelper->getUserEditRoles();

        if (empty($canEditRole) || in_array($roleData->getRoleId(), $canEditRole)) {

            $baseFieldset->addField(
                'vendor',
                'select',
                [
                    'name' => 'vendor',
                    'label' => __('Vendor'),
                    'id' => 'vendor',
                    'title' => __('Vendor'),
                    'class' => 'input-select',
                    'options' => $this->getVendorsArray(false),
                    'value' => $this->getCurrentVendor($model->getUserId())
                ]
            );

            $baseFieldset->addField(
                'store_ids',
                'multiselect',
                [
                    'name' => 'store_ids[]',
                    'label' => __('Store Views'),
                    'title' => __('Store Views'),
                    'required' => false,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'value' => $this->getCurrentStores($model->getUserId())
                ]
            )->setAfterElementHtml('
            <div class="field-tooltip toggle">
                <span class="field-tooltip-action action-help" tabindex="0" hidden="hidden"></span>
                <div class="field-tooltip-content">
                     <span>* No selected store view will grant access to all stores/website views by default</span>
                </div>
            </div>
            ');
        }

        return $this;
    }

    public function getCurrentVendor($userId)
    {
        if (!empty($userId)) {
            $connection = $this->_resourceConnection->getConnection();
            $tableName = $connection->getTableName($this->getAdminUserTable());
            $sql = "Select `vendor_id` FROM " . $tableName . " WHERE `user_id` = " . $userId;
            $vendorId = $connection->fetchOne($sql);

            if (!empty($vendorId) && count($vendorId) > 0) {
                return $vendorId;
            } else {
                return false;
            }
        }
    }

    public function getCurrentStores($userId)
    {
        if (!empty($userId)) {
            $connection = $this->_resourceConnection->getConnection();
            $tableName = $connection->getTableName($this->getUserStoresTable());
            $sql = "Select `store_id` FROM " . $tableName . " WHERE `user_id` = " . $userId;
            $userStores = $connection->fetchOne($sql);

            if (!empty($userStores) && count($userStores) > 0) {
                $userStoresUnserialized = unserialize(($userStores));
                return $userStoresUnserialized;
            } else {
                return false;
            }
        }
    }

    public function getAdminUserTable()
    {
        return 'omnyfy_vendor_vendor_admin_user';
    }

    public function getUserStoresTable()
    {
        return 'omnyfy_vendor_vendor_user_stores';
    }

    /**
     * @return array
     */
    public function getVendorsArray($activeOnly = true)
    {
        /** @var \Omnyfy\Vendor\Model\Resource\Vendor\Collection $vendorCollection */
        $vendorCollection = $this->_vendorCollectionFactory->create();
        if ($activeOnly) {
            $vendorCollection->addFieldToFilter('status', \Omnyfy\Vendor\Model\Source\Status::STATUS_ACTIVE);
        }

        $vendorCollection->load();

        $options = [];
        $options['none'] = 'None';
        /** @var \Omnyfy\Vendor\Model\Vendor $vendor */
        foreach($vendorCollection as $vendor){
            $options[$vendor->getEntityId()] = $vendor->getName();
        }

        return $options;

    }

    /**
     * Retrieve dropdown data
     */
    public function getVendorDropdown()
    {

        $content = $this->_scopeConfig->getValue('AdminTabs/general/dropdown');
        $lines = explode(";", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                echo '<option value="' . $line . '">' . $line . '</option>';
            }
        }
    }
}