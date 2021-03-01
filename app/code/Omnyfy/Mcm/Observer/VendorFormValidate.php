<?php
/**
 * Project: MCM
 * User: jing
 * Date: 2019-05-31
 * Time: 14:54
 */
namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class VendorFormValidate implements ObserverInterface
{
    protected $_config;

    public function __construct(
        \Omnyfy\Mcm\Model\Config $config
    )
    {
        $this->_config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_config->isIncludeKyc()) {
            return;
        }

        $formData = $observer->getData('form_data');
        $error = $this->validateBankAccount($formData);

        if ($error) {
            throw new LocalizedException(__($error));
        }
    }

    public function validateBankAccount($data) {

        if (!isset($data['account_name']) || empty($data['account_name'])) {
            return 'Please enter a Bank Account Name.';
        } else if (strlen($data['account_name']) > 100) {
            return 'Please enter less or equal than 100 symbols in Bank Account Name.';
        }

        if (!isset($data['bsb']) || empty($data['bsb'])) {
            return 'Please enter a BSB.';

        } else if (strlen($data['bsb']) > 20) {
            return 'Please enter less or equal than 20 symbols in BSB.';

        } else if (!is_numeric($data['bsb'])) {
            return 'Please enter a valid number in BSB.';

        }

        if (!isset($data['account_number']) || empty($data['account_number'])) {
            return 'Please enter a Account Number.';

        } else if (strlen($data['account_number']) > 20) {
            return 'Please enter less or equal than 20 symbols in Account Number.';

        } else if (!is_numeric($data['account_number'])) {
            return 'Please enter a valid number in Account Number.';

        }

        if (!isset($data['account_type_id']) || empty($data['account_type_id'])) {
            return 'Please select a Account Type.';

        } else {
            if ($data['account_type_id'] == 2) {
                if (!isset($data['swift_code']) || empty($data['swift_code'])) {
                    return 'Please enter a SWIFT Code.';

                } else if (strlen($data['swift_code']) > 30) {
                    return 'Please enter less or equal than 30 symbols in SWIFT Code.';

                } else if (!ctype_alnum($data['swift_code'])) {
                    return 'Please use only letters (a-z or A-Z) or numbers (0-9) in this field. No spaces or other characters are allowed in SWIFT Code.';

                }
            }
        }
        if (strlen($data['bank_name']) > 200) {
            return 'Please enter less or equal than 200 symbols in Bank Name.';

        }
        if (strlen($data['bank_address']) > 200) {
            return 'Please enter less or equal than 200 symbols in Bank Address.';

        }
//        if (strlen($data['company_name']) > 200) {
//            return 'Please enter less or equal than 200 symbols in Company Name.';
//
//        }

        return false;
    }
}
 