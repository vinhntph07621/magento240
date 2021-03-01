<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Import\Category\Validation;

use Amasty\Base\Model\Import\AbstractImport;
use Amasty\Base\Model\Import\Validation\Validator;
use Amasty\Faq\Api\ImportExport\CategoryInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

class Stores extends Validator implements \Amasty\Base\Model\Import\Validation\ValidatorInterface
{
    const ERROR_UNKNOWN_STORE_CODE = 'unknownStoreCode';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::ERROR_UNKNOWN_STORE_CODE => '<b>Error!</b> Unknown Store Code'
    ];

    /**
     * @var array
     */
    private $stores = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Framework\DataObject $validationData,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            $this->stores[$store->getCode()] = $store->getId();
        }
        parent::__construct($validationData);
    }

    /**
     * @inheritdoc
     */
    public function validateRow(array $rowData, $behavior)
    {
        $this->errors = [];
        $stores = [];
        $this->validationData->unsetData('stores');

        if (empty($rowData[CategoryInterface::STORE_CODES])) {
            $stores[] = $this->storeManager->getDefaultStoreView()->getId();
        } else {
            $storeCodes = explode(
                AbstractImport::MULTI_VALUE_SEPARATOR,
                $rowData[CategoryInterface::STORE_CODES]
            );
            foreach ($storeCodes as $code) {
                $code = trim($code);
                if (isset($this->stores[$code])) {
                    $stores[] = $this->stores[$code];
                } else {
                    $this->errors[self::ERROR_UNKNOWN_STORE_CODE] = ProcessingError::ERROR_LEVEL_CRITICAL;
                    break;
                }
            }
        }
        if (!isset($this->errors[self::ERROR_UNKNOWN_STORE_CODE]) && !empty($stores)) {
            $this->validationData->setData('stores', $stores);
        }

        return $this->validateResult();
    }
}
