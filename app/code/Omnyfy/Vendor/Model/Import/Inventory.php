<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 28/1/20
 * Time: 12:28 pm
 */
namespace Omnyfy\Vendor\Model\Import;
use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

class Inventory extends \Magento\ImportExport\Model\Import\AbstractEntity
{
    const COL_ID = 'inventory_id';

    const COL_PRODUCT_ID = 'product_id';

    const COL_SKU = 'sku';

    const COL_LOCATION_ID = 'location_id';

    const COL_QTY = 'qty';

    const ENTITY_TYPE_CODE = 'omnyfy_vendor_inventory';

    const TABLE = 'omnyfy_vendor_inventory';

    const ERROR_CODE_REQUIRED_VALUE = 'idEmpty';

    const ERROR_CODE_PRODUCT_NOT_EXIST = 'productNotExist';

    const ERROR_CODE_LOCATION_NOT_EXIST = 'locationNotExist';

    const ERROR_CODE_SKU_ID_NOT_MATCH = 'skuNotMatch';

    const ERROR_CODE_ASSIGNED_VENDOR_ALREADY = 'vendorAlready';

    const ERROR_CODE_LOCATION_NOT_BELONG = 'locationNotBelong';

    protected $_vendorResource;

    protected $_config;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    protected $needColumnCheck = true;

    protected $logInHistory = true;

    protected $_cachedIds;

    protected $_tableName;

    protected $_locationIdToVendorId;

    protected $validColumnNames = [
        self::COL_ID,
        self::COL_SKU,
        self::COL_PRODUCT_ID,
        self::COL_LOCATION_ID,
        self::COL_QTY
    ];

    protected $_requiredColumns = [
        self::COL_LOCATION_ID,
        self::COL_QTY,
    ];

    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Model\Config $config,
        \Magento\Backend\Model\Session $backendSession,
        array $data = [])
    {
        $this->_vendorResource = $vendorResource;
        $this->_config = $config;
        $this->_backendSession = $backendSession;
        parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $data);
        $this->errorMessageTemplates[self::ERROR_CODE_REQUIRED_VALUE] = 'Column "%s" is required';
        $this->errorMessageTemplates[self::ERROR_CODE_PRODUCT_NOT_EXIST] = 'Product "%s" does not exist';
        $this->errorMessageTemplates[self::ERROR_CODE_LOCATION_NOT_EXIST] = 'Location "%s" does not exist';
        $this->errorMessageTemplates[self::ERROR_CODE_SKU_ID_NOT_MATCH] = 'Product "%s" SKU and ID  mismatched';
        $this->errorMessageTemplates[self::ERROR_CODE_ASSIGNED_VENDOR_ALREADY] = 'Product already assigned to vendor "%s"';
        $this->errorMessageTemplates[self::ERROR_CODE_LOCATION_NOT_BELONG] = 'Location "%s" is not your location';
        $this->masterAttributeCode = self::COL_LOCATION_ID;
    }

    /**
     * @inheritDoc
     */
    protected function _importData()
    {
        switch($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                $this->deleteInventories();
                break;
            case Import::BEHAVIOR_REPLACE:
                $this->replaceInventories();
                break;
            case Import::BEHAVIOR_APPEND:
            case Import::BEHAVIOR_ADD_UPDATE:
                $this->saveInventories();
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }

    /**
     * @inheritDoc
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;

        // BEHAVIOR_DELETE use specific validation logic
        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (!isset($rowData[self::COL_ID]) || empty($rowData[self::COL_ID])) {
                $this->addRowError(self::ERROR_CODE_REQUIRED_VALUE, $rowNum, self::COL_ID);
                return false;
            }
            return true;
        }

        foreach ($this->_requiredColumns as $col) {
            if (!isset($rowData[$col]) || empty($rowData[$col])) {
                $this->addRowError(self::ERROR_CODE_REQUIRED_VALUE, $rowNum, $col);
                return false;
            }
        }

        if (!isset($rowData[self::COL_PRODUCT_ID]) && !isset($rowData[self::COL_SKU])) {
            $this->addRowError(self::ERROR_CODE_REQUIRED_VALUE, $rowNum, self::COL_PRODUCT_ID . ' or ' . self::COL_SKU );
            return false;
        }

        if (isset($rowData[self::COL_SKU])) {
            $productId = $this->_getProductIdBySku($rowData[self::COL_SKU]);
            if (empty($productId)) {
                $this->addRowError(self::ERROR_CODE_PRODUCT_NOT_EXIST, $rowNum, $rowData[self::COL_SKU]);
                return false;
            }
            if (isset($rowData[self::COL_PRODUCT_ID]) && $productId != $rowData[self::COL_PRODUCT_ID]) {
                $this->addRowError(self::ERROR_CODE_SKU_ID_NOT_MATCH, $rowNum, $rowData[self::COL_SKU] . ' and ' . $rowData[self::COL_PRODUCT_ID]);
                return false;
            }
            $rowData[self::COL_PRODUCT_ID] = $productId;
        }

        if (isset($rowData[self::COL_PRODUCT_ID]) && !$this->_isProductIdExists($rowData[self::COL_PRODUCT_ID])) {
            $this->addRowError(self::ERROR_CODE_PRODUCT_NOT_EXIST, $rowNum, $rowData[self::COL_PRODUCT_ID]);
            return false;
        }

        $vendorId = $this->_getVendorIdByLocationId($rowData[self::COL_LOCATION_ID]);
        if (empty($vendorId)) {
            $this->addRowError(self::ERROR_CODE_LOCATION_NOT_EXIST, $rowNum, $rowData[self::COL_LOCATION_ID]);
            return false;
        }

        $msg = null;
        //check product-vendor relationship
        if (!$this->_isVendorValid($rowData[self::COL_PRODUCT_ID], $vendorId, $msg)) {
            $this->addRowError(self::ERROR_CODE_ASSIGNED_VENDOR_ALREADY, $rowNum, $msg);
            return false;
        }

        //Vendor should not import other vendors' inventory
        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (!empty($vendorInfo)) {
            if (!$this->_config->isVendorShareProducts() && $vendorId != $vendorInfo['vendor_id']) {
                $this->addRowError(self::ERROR_CODE_LOCATION_NOT_BELONG, $rowNum, $rowData[self::COL_LOCATION_ID]);
                return false;
            }
        }

        return true;
    }

    public function saveInventories()
    {
        $this->_saveAndReplaceInventories();
        return $this;
    }

    public function replaceInventories()
    {
        $this->_saveAndReplaceInventories();
        return $this;
    }

    public function deleteInventories()
    {
        $listId = [];
        while($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);

                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $listId[] = $rowData[self::COL_ID];
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }

        if ($listId) {
            $this->_deleteInventories(array_unique($listId));
        }

        return $this;
    }

    protected function _saveAndReplaceInventories()
    {
        $this->loadLocationIdToVendorId();
        $behavior = $this->getBehavior();

        while($bunch = $this->_dataSourceModel->getNextBunch()) {
            $data = [];
            $listId = [];
            foreach($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $_rowData = [
                    'sku'         => isset($rowData[self::COL_SKU]) ? $rowData[self::COL_SKU] : null,
                    'location_id' => $rowData[self::COL_LOCATION_ID],
                    'product_id'  => isset($rowData[self::COL_PRODUCT_ID]) ? $rowData[self::COL_PRODUCT_ID] : null,
                    'qty'         => $rowData[self::COL_QTY]
                ];

                if (isset($rowData[self::COL_ID])) {
                    if (Import::BEHAVIOR_APPEND == $behavior) {
                        $_rowData[self::COL_ID] = $rowData[self::COL_ID];
                        $listId[] = $rowData[self::COL_ID];
                    }

                    if (Import::BEHAVIOR_REPLACE == $behavior) {
                        $listId[] = $rowData[self::COL_ID];
                    }
                }

                $data[] = $_rowData;
            }

            switch ($behavior) {
                case Import::BEHAVIOR_REPLACE:
                    if ($data) {
                        if ($this->_deleteInventories(array_unique($listId))) {
                            $this->_saveInventories($data);
                        }
                    }
                    break;
                case Import::BEHAVIOR_APPEND:
                case Import::BEHAVIOR_ADD_UPDATE:
                    $this->_saveInventories($data);
                    break;
            }
        }

        return $this;
    }

    protected function _getTableName()
    {
        if (!$this->_tableName) {
            $this->_tableName = $this->_connection->getTableName('omnyfy_vendor_inventory');
        }

        return $this->_tableName;
    }

    protected function _idExists($id)
    {
        if (!$this->_cachedIds) {
            $this->_cachedIds = $this->_connection->fetchCol(
                $this->_connection->select()->from($this->_getTableName(), self::COL_ID)
            );
        }

        return in_array($id, $this->_cachedIds);
    }

    protected function _saveInventories(array $data)
    {
        if ($data) {
            $tableName = $this->_getTableName();

            $skus = [];
            foreach($data as $row) {
                if (isset($row[self::COL_SKU]) && !isset($row[self::COL_PRODUCT_ID])) {
                    $skus[] = $row[self::COL_SKU];
                }
            }

            $skuToProductIds = $this->_loadProductIdsBySkus($skus);
            $productIdToVendorId = [];

            foreach($data as $row) {
                if (isset($row[self::COL_SKU])) {
                    if (!isset($row[self::COL_PRODUCT_ID])) {
                        $productId = $skuToProductIds[$row[self::COL_SKU]];
                        $row[self::COL_PRODUCT_ID] = $productId;
                    }
                    unset($row[self::COL_SKU]);
                }
                if (isset($row[self::COL_ID]) && $this->_idExists($row[self::COL_ID])) {
                    $id = $row[self::COL_ID];
                    unset($row[self::COL_ID]);

                    if ($this->_connection->update($tableName, $row, $this->_connection->quoteInto(self::COL_ID . ' = ?', $id))) {
                        $this->countItemsUpdated++;
                    }
                }
                elseif ($this->_connection->insertOnDuplicate($tableName, $row, ['qty'])) {
                    $this->countItemsCreated++;
                }

                //prepare data to update product-vendor relationship
                $vendorId = $this->_locationIdToVendorId[$row[self::COL_LOCATION_ID]];
                $productIdToVendorId[] = array(
                    'product_id' => $row[self::COL_PRODUCT_ID],
                    'vendor_id' => $vendorId
                );
            }
            $this->_vendorResource->saveProductRelation($productIdToVendorId);
        }
        return $this;
    }

    protected function _deleteInventories(array $listId)
    {
        $tableName = $this->_getTableName();
        if (!empty($tableName) && !empty($listId)) {
            try {
                $where = $this->_connection->quoteInto(self::COL_ID . ' IN (?)', $listId);
                $this->countItemsDeleted += $this->_connection->delete($tableName, $where);
                return true;
            }
            catch (\Exception $e) {

            }
        }

        return false;
    }

    protected function _isProductIdExists($productId)
    {
        $tableName = $this->_connection->getTableName('catalog_product_entity');
        if (!empty($tableName) && !empty($productId)) {
            try {
                $resultId = $this->_connection->fetchOne(
                    $this->_connection->select()->from($tableName, 'entity_id')
                        ->where('entity_id=?', $productId)
                );
                if (!empty($resultId)) {
                    return true;
                }
            }
            catch(\Exception $e)
            {

            }
        }

        return false;
    }

    protected function _isVendorValid($productId, $vendorId, &$msg)
    {
        $vendorIds = $this->_vendorResource->getVendorIdArrayByProductId($productId);
        if ($this->_config->isVendorShareProducts()) {
            return true;
        }

        if (empty($vendorIds) || in_array($vendorId, $vendorIds)) {
            return true;
        }

        $msg = $vendorIds[0] . '-' . $productId;
        return false;
    }

    protected function _getVendorIdByLocationId($locationId)
    {
        $tableName = $this->_connection->getTableName('omnyfy_vendor_location_entity');
        if (!empty($tableName) && !empty($locationId)) {
            try {
                $resultId = $this->_connection->fetchOne(
                    $this->_connection->select()->from($tableName, 'vendor_id')
                        ->where('entity_id=?', $locationId)
                );
                if (!empty($resultId)) {
                    return $resultId;
                }
            }
            catch(\Exception $e)
            {

            }
        }

        return false;
    }

    protected function _getProductIdBySku($sku)
    {
        $tableName = $this->_connection->getTableName('catalog_product_entity');
        if (!empty($tableName) && !empty($sku)) {
            try {
                $resultId = $this->_connection->fetchOne(
                    $this->_connection->select()->from($tableName, 'entity_id')
                        ->where('sku=?', $sku)
                );
                if (!empty($resultId)) {
                    return $resultId;
                }
            }
            catch(\Exception $e)
            {

            }
        }
        return false;
    }

    protected function _loadProductIdsBySkus($skus)
    {
        $tableName = $this->_connection->getTableName('catalog_product_entity');
        $result = [];
        if (!empty($tableName) && !empty($skus)) {
            try {
                $data = $this->_connection->fetchAll(
                    $this->_connection->select()->from($tableName, array('entity_id', 'sku'))
                        ->where('sku IN (?)', $skus)
                );
                foreach($data as $_row) {
                    $result[$_row['sku']] = $_row['entity_id'];
                }
            }
            catch(\Exception $e)
            {

            }
        }
        return $result;
    }

    protected function loadLocationIdToVendorId()
    {
        if (empty($this->_locationIdToVendorId)) {
            $tableName = $this->_connection->getTableName('omnyfy_vendor_location_entity');
            $result = [];
            $data = $this->_connection->fetchAll(
                $this->_connection->select()->from($tableName, array('entity_id', 'vendor_id'))
            );
            foreach($data as $_row) {
                $result[$_row['entity_id']] = $_row['vendor_id'];
            }
            $this->_locationIdToVendorId = $result;
        }

        return $this->_locationIdToVendorId;
    }
}