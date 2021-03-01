<?php
/**
 * Project: Vendor SignUp
 * User: jing
 * Date: 2019-08-05
 * Time: 16:43
 */
namespace Omnyfy\VendorSignUp\Helper;

use Magento\Framework\App\Helper\Context;
use Omnyfy\Mcm\Model\ResourceModel\VendorBankAccount;

class Backend extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_roleCollection;

    protected $_vendorTypeRepository;

    protected $kycCollectionFactory;

    protected $signUpCollectionFactory;

    protected $bankAccountCollectionFactory;

    protected $countryFactory;

    protected $countryCodes = [];

    public function __construct(
        Context $context,
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollection,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository,
        \Omnyfy\VendorSignUp\Model\ResourceModel\VendorKyc\CollectionFactory $kycCollectionFactory,
        \Omnyfy\VendorSignUp\Model\ResourceModel\SignUp\CollectionFactory $signUpCollectionFactory,
        \Omnyfy\Mcm\Model\ResourceModel\VendorBankAccount\CollectionFactory $bankAccountCollectionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
        $this->_roleCollection = $roleCollection;
        $this->_vendorTypeRepository = $vendorTypeRepository;
        $this->kycCollectionFactory = $kycCollectionFactory;
        $this->signUpCollectionFactory = $signUpCollectionFactory;
        $this->bankAccountCollectionFactory = $bankAccountCollectionFactory;
        $this->countryFactory = $countryFactory;
        parent::__construct($context);
    }

    public function getRoleId($signUp)
    {
        /* Checking Role Id via role name */
        $roleCollection = $this->_roleCollection->create();
        $roleCollection->addFieldToFilter('role_type', 'G');
        $roleCollection->addFieldToFilter('role_name', 'Vendor Admin');
        $role = $roleCollection->getFirstItem();
        return $role->getId();
    }

    public function getVendorType($typeId)
    {
        return $this->_vendorTypeRepository->getById($typeId);
    }

    public function getKycByVendorId($vendorId)
    {
        $collection = $this->kycCollectionFactory->create();
        $collection->addFieldToFilter('vendor_id', $vendorId)
            ->setOrder('created_at', 'desc')
            ->setPageSize(1);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return false;
    }

    public function getKycBySignUpId($signUpId)
    {
        $collection = $this->kycCollectionFactory->create();
        $collection->addFieldToFilter('signup_id', $signUpId)
            ->setOrder('created_at', 'desc')
            ->setPageSize(1);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return false;
    }

    public function getSignUpById($signUpId)
    {
        $collection = $this->signUpCollectionFactory->create();
        $collection->addFieldToFilter('id', $signUpId)
            ->setPageSize(1);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return false;
    }

    public function getBankAccountByVendorId($vendorId)
    {
        $collection = $this->bankAccountCollectionFactory->create();
        $collection->addFieldToFilter('vendor_id', $vendorId)
            ->setPageSize(1);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return false;
    }

    public function getCountryISO3Code($code)
    {
        if (!isset($this->countryCodes[$code])) {
            $country = $this->countryFactory->create()->loadByCode($code);
            $this->countryCodes[$code] = $country['iso3_code'];
        }
        return $this->countryCodes[$code];
    }
}
 