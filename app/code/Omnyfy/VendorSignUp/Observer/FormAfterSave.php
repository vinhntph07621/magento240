<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-28
 * Time: 17:57
 */
namespace Omnyfy\VendorSignUp\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class FormAfterSave implements ObserverInterface
{
    protected $signUpFactory;

    protected $kycFactory;

    protected $kycCollectionFactory;

    protected $signUpCollectionFactory;

    protected $_helper;

    protected $countryFactory;

    protected $payoutResource;

    protected $_config;

    protected $countryCodes = [];

    protected $fieldsToCheck = [
        'first_name',
        'last_name',
        'dob',
        'business_name',
        'business_address',
        'city',
        'state',
        'country',
        'postcode',
        'country_code',
        'telephone',
        'kyc_email',
        'legal_entity',
        'tax_number',
        'abn',
        'account_type_id',
        'acc_country',
        'account_name',
        'bank_name',
        'bsb',
        'account_number',
        'account_type',
        'holder_type',
        'bank_address',
        'swift_code'
    ];

    public function __construct(
        \Omnyfy\VendorSignUp\Model\SignUpFactory $signUpFactory,
        \Omnyfy\VendorSignUp\Model\VendorKycFactory $kycFactory,
        \Omnyfy\VendorSignUp\Model\ResourceModel\VendorKyc\CollectionFactory $kycCollectionFactory,
        \Omnyfy\VendorSignUp\Model\ResourceModel\SignUp\CollectionFactory $signUpCollectionFactory,
        \Omnyfy\VendorSignUp\Helper\Data $helper,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Omnyfy\Mcm\Model\ResourceModel\VendorPayout $payoutResource,
        \Omnyfy\Mcm\Model\Config $config
    )
    {
        $this->signUpFactory = $signUpFactory;
        $this->kycFactory = $kycFactory;
        $this->kycCollectionFactory = $kycCollectionFactory;
        $this->signUpCollectionFactory = $signUpCollectionFactory;
        $this->_helper = $helper;
        $this->countryFactory = $countryFactory;
        $this->payoutResource = $payoutResource;
        $this->_config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendor = $observer->getData('vendor');
        $formData = $observer->getData('form_data');
        $isNew = $observer->getData('is_new');

        $vendor->setData('abn', $formData['abn']);

        if ($isNew) {
            // save sign up
            $signUp = $this->saveSignUp($formData, $vendor);

            // save KYC
            $kyc = $this->saveKycDetails([
                'vendor_id' => $vendor->getId(),
                'signup_id' => $signUp->getId(),
                'kyc_status' => 'pending'
            ]);

        } else {
            $signUp = null;

            // load KYC by vendor_id
            $kyc = $this->getKycByVendorId($vendor->getId());

            // load sign up by signup_id in kyc
            if (!empty($kyc)) {
                $signUpId = $kyc->getSignupId();

                $signUp = $this->getSignUpById($signUpId);
            }

            if (empty($signUp)) {
                $signUp = $this->saveSignUp($formData, $vendor);
            }
            else{
                $this->updateSignUp($signUp, $formData, $vendor);
            }

            if (empty($kyc)) {
                $kyc = $this->saveKycDetails([
                    'vendor_id' => $vendor->getId(),
                    'signup_id' => $signUp->getId(),
                    'kyc_status' => 'pending'
                ]);
            }
        }
    }

    protected function saveSignUp($data, $vendor)
    {
        $signUp = $this->signUpFactory->create();
        $signUp->setData([
            'business_name' => $vendor->getName(),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'dob' => $data['dob'],
            'business_address' => $data['business_address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'postcode' => $data['postcode'],
            'country_code' => $data['country_code'],
            'telephone' => $data['telephone'],
            'email' => $vendor->getEmail(),
            'legal_entity' => $data['legal_entity'],
            'tax_number' => isset($data['tax_number']) ? $data['tax_number'] : null,
            'abn' => $data['abn'],
            'status' => '0',
            'created_by' => 'Admin',
            'created_at' => '',
            'vendor_type_id' => $vendor->getTypeId()
        ]);

        $signUp->save();
        return $signUp;
    }

    protected function updateSignUp($signUp, $data, $vendor)
    {
        $signUp->addData([
            'business_name' => $vendor->getName(),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'dob' => $data['dob'],
            'business_address' => $data['business_address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'postcode' => $data['postcode'],
            'country_code' => $data['country_code'],
            'telephone' => $data['telephone'],
            'email' => $vendor->getEmail(),
            'legal_entity' => $data['legal_entity'],
            'tax_number' => isset($data['tax_number']) ? $data['tax_number'] : null,
            'abn' => $data['abn'],
            'vendor_type_id' => $vendor->getTypeId()
        ]);

        $signUp->save();

    }

    protected function saveKycDetails($data)
    {
        $kyc = $this->kycFactory->create();
        $kyc->setData($data);
        $kyc->save();
        return $kyc;
    }

    protected function getKycByVendorId($vendorId)
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

    protected function getSignUpById($signUpId)
    {
        $collection = $this->signUpCollectionFactory->create();
        $collection->addFieldToFilter('id', $signUpId)
            ->setPageSize(1);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return false;
    }

    protected function formatAccountData($data)
    {
        return [
            'id' => $this->_helper->uuidByEmail($data['kyc_email']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['kyc_email'],
            'mobile' => $data['country_code'].$data['telephone'],
            'address_line1' => $data['business_address'],
            'state' => $data['state'],
            'city' => $data['city'],
            'zip' => $data['postcode'],
            'country' => $data['country'],
            'dob' => date('d/m/Y', strtotime($data['dob'])),
            'tax_number' => $data['abn']
        ];
    }

    protected function formatCompanyData($kycUserId, $data, $kycCompanyId=null)
    {
        $result = [
            "user_id" => $kycUserId,
            "name" => $data['business_name'],
            "legal_name" => $data['legal_entity'],
            "tax_number" => $data['abn'],
            'address_line1' => $data['business_address'],
            'state' => $data['state'],
            'city' => $data['city'],
            'zip' => $data['postcode'],
            'phone' => $data['country_code'].$data['telephone'],
            "country" => $this->getCountryISO3Code($data['acc_country'])
        ];
        if (!empty($kycCompanyId)) {
            $result['id'] = $kycCompanyId;
        }
        return $result;
    }

    protected function formatBankAccountData($kycUserId, $data)
    {
        return [
            "user_id" => $kycUserId,
            "bank_name" => $data['bank_name'],
            "account_name" => $data['account_name'],
            "routing_number" => ($data['account_type_id']==1)?$data['bsb']:$data['swift_code'],
            "account_number" => $data['account_number'],
            "account_type" => $data['account_type'],
            "holder_type" => $data['holder_type'],
            "country" => $this->getCountryISO3Code($data['acc_country'])
        ];
    }

    protected function loadErrors($data)
    {
        $result = [];
        if (isset($data['errors'])) {
            if (is_array($data['errors'])) {
                foreach($data['errors'] as $key => $val) {
                    if (is_array($val)) {
                        $result[$key] = $val[0];
                    }
                    else{
                        $result[$key] = $val;
                    }
                }
            }
            else {
                $result['unknown'] = 'Something wrong with gateway';
            }
        }
        return $result;
    }

    protected function loadErrorString($data)
    {
        $errors = $this->loadErrors($data);
        if (empty($errors)) {
            return false;
        }

        $result = '';
        foreach($errors as $key => $val) {
            $result .= 'Invalid user KYC info '. $key. ' '. $val . "\n";
        }
        return $result;
    }

    protected function getCountryISO3Code($code)
    {
        if (!isset($this->countryCodes[$code])) {
            $country = $this->countryFactory->create()->loadByCode($code);
            $this->countryCodes[$code] = $country['iso3_code'];
        }
        return $this->countryCodes[$code];
    }
}
 
