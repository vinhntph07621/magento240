<?php

namespace Omnyfy\Postcode\Model\Import;

class Postcode extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{

    const COL_ID        = 'id';
    const COL_COUNTRY   = 'country_code';
    const COL_REGION    = 'region_code';
    const COL_POSTCODE  = 'postcode';
    const COL_SUBURB    = 'suburb';
    const COL_LATITUDE  = 'latitude';
    const COL_LONGITUDE = 'longitude';
    const COL_TIMEZONE  = 'timezone';

    const DEFAULT_ALL_GROUPS_GROUPED_PRICE_VALUE = '0';

    const ENTITY_TYPE_CODE = 'omnyfy_postcode';

    const ERROR_CODE_REQUIRED_VALUE = 'idEmpty';
    const ERROR_CODE_INVALID_COUNTRY = 'invalidCountry';

    protected $errorMessageTemplates = [
        self::ERROR_CODE_REQUIRED_VALUE => 'Column "%s" is required',
        self::ERROR_CODE_INVALID_COUNTRY => 'Invalid country code value',

        // Copied from parent
        self::ERROR_CODE_SYSTEM_EXCEPTION => 'General system exception happened',
        self::ERROR_CODE_COLUMN_NOT_FOUND => 'We can\'t find required columns: %s.',
        self::ERROR_CODE_COLUMN_EMPTY_HEADER => 'Columns number: "%s" have empty headers',
        self::ERROR_CODE_COLUMN_NAME_INVALID => 'Column names: "%s" are invalid',
        self::ERROR_CODE_ATTRIBUTE_NOT_VALID => "Please correct the value for '%s'.",
        self::ERROR_CODE_DUPLICATE_UNIQUE_ATTRIBUTE => "Duplicate Unique Attribute for '%s'",
        self::ERROR_CODE_ILLEGAL_CHARACTERS => "Illegal character used for attribute %s",
        self::ERROR_CODE_INVALID_ATTRIBUTE => 'Header contains invalid attribute(s): "%s"',
        self::ERROR_CODE_WRONG_QUOTES => "Curly quotes used instead of straight quotes",
        self::ERROR_CODE_COLUMNS_NUMBER => "Number of columns does not correspond to the number of rows in the header",
        // --
    ];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Valid column names
     *
     * @var string[]
     */
    protected $validColumnNames = [
        self::COL_ID,
        self::COL_COUNTRY,
        self::COL_REGION,
        self::COL_POSTCODE,
        self::COL_SUBURB,
        self::COL_LATITUDE,
        self::COL_LONGITUDE,
        self::COL_TIMEZONE,
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * Required columns
     *
     * @var string[]
     */
    protected $_requiredColumns = [
        self::COL_COUNTRY,
        self::COL_POSTCODE,
        self::COL_SUBURB,
        self::COL_LATITUDE,
        self::COL_LONGITUDE,
    ];

    /**
     * @var array
     */
    protected $_cachedCountries;

    /**
     * @var array
     */
    protected $_cachedIds;

    /**
     * @var string
     */
    protected $_tableName;

    /**
     * Check country code
     *
     * @param string $countryCode
     * @return boolean
     */
    protected function _isCountryValid($countryCode)
    {
        if (!$this->_cachedCountries) {
            $countryTable = $this->_connection->getTableName('directory_country');
            $this->_cachedCountries = $this->_connection->fetchCol(
                $this->_connection->select()->from($countryTable, 'country_id')
            );
        }

        return in_array($countryCode, $this->_cachedCountries);
    }

    /**
     * Check if id exists
     *
     * @param int $id
     * @return boolean
     */
    protected function _idExists($id)
    {
        if (!$this->_cachedIds) {
            $this->_cachedIds = $this->_connection->fetchCol(
                $this->_connection->select()->from($this->_getTableName(), 'postcode_id')
            );
        }

        return in_array($id, $this->_cachedIds);
    }

    /**
     * Get table name
     *
     * @return string
     */
    protected function _getTableName()
    {
        if (!$this->_tableName) {
            $this->_tableName = $this->_connection->getTableName(\Omnyfy\Postcode\Model\ResourceModel\Postcode::TABLE_NAME);
        }

        return $this->_tableName;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }

    /**
     * Validate row
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;

        // BEHAVIOR_DELETE use specific validation logic
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
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

        if (!$this->_isCountryValid($rowData[self::COL_COUNTRY])) {
            $this->addRowError(self::ERROR_CODE_INVALID_COUNTRY, $rowNum);
            return false;
        }

        return true;
    }

    /**
     * Create postcode data from raw data.
     *
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        switch ($this->getBehavior()) {
            case \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE:
                $this->deletePostcodes();
                break;
            case \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE:
                $this->replacePostcodes();
                break;
            case \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND:
            case \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE:
                $this->savePostcodes();
                break;
        }

        return true;
    }

    /**
     * Save postcodes
     *
     * @return \Omnyfy\Postcode\Model\Import\Postcode
     */
    public function savePostcodes()
    {
        $this->_saveAndReplacePostcodes();

        return $this;
    }

    /**
     * Deletes postcode data from raw data.
     *
     * @return \Omnyfy\Postcode\Model\Import\Postcode
     */
    public function deletePostcodes()
    {
        $listId = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
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
            $this->_deletePostcodes(array_unique($listId));
        }

        return $this;
    }

    /**
     * Replace postcodes
     *
     * @return \Omnyfy\Postcode\Model\Import\Postcode
     */
    public function replacePostcodes()
    {
        $this->_saveAndReplacePostcodes();

        return $this;
    }

    /**
     * Save and replace advanced prices
     *
     * @return \Omnyfy\Postcode\Model\Import\Postcode
     */
    protected function _saveAndReplacePostcodes()
    {
        $behavior = $this->getBehavior();

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $data = [];
            $listId = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $_rowData = [
                    'country_id'        => $rowData[self::COL_COUNTRY],
                    'region_code'       => $rowData[self::COL_REGION],
                    'postcode'          => $rowData[self::COL_POSTCODE],
                    'suburb'            => $rowData[self::COL_SUBURB],
                    'latitude'          => $rowData[self::COL_LATITUDE],
                    'longitude'         => $rowData[self::COL_LONGITUDE],
                    'timezone_override' => $rowData[self::COL_TIMEZONE],
                ];

                if (isset($rowData[self::COL_ID])) {
                    if (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                        $_rowData['postcode_id'] = $rowData[self::COL_ID];
                        $listId[] = $rowData[self::COL_ID];
                    }

                    if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                        $listId[] = $rowData[self::COL_ID];
                    }
                }

                $data[] = $_rowData;
            }

            switch ($behavior) {
                case \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE:
                    if ($data) {
                        if ($this->_deletePostcodes(array_unique($listId))) {
                            $this->_savePostcodes($data);
                        }
                    }
                    break;

                case \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND:
                case \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE:
                    $this->_savePostcodes($data);
                    break;
            }
        }

        return $this;
    }

    /**
     * Save postcodes
     *
     * @param array $data
     * @return \Omnyfy\Postcode\Model\Import\Postcode
     */
    protected function _savePostcodes(array $data)
    {
        if ($data) {
            $tableName = $this->_getTableName();

            foreach ($data as $row) {
                if (isset($row['postcode_id']) && $this->_idExists($row['postcode_id'])) {
                    $id = $row['postcode_id'];
                    unset($row['postcode_id']);

                    if ($this->_connection->update($tableName, $row, $this->_connection->quoteInto('postcode_id = ?', $id))) {
                        $this->countItemsUpdated++;
                    }
                } elseif ($this->_connection->insert($tableName, $row)) {
                    $this->countItemsCreated++;
                }
            }
        }

        return $this;
    }

    /**
     * Deletes postcodes
     *
     * @param array $listId
     * @param string $tableName
     * @return bool
     */
    protected function _deletePostcodes(array $listId)
    {
        $tableName = $this->_getTableName();
        if ($tableName && $listId) {
            try {
                $where = $this->_connection->quoteInto('postcode_id IN (?)', $listId);
                $this->countItemsDeleted += $this->_connection->delete($tableName, $where);

                return true;
            } catch (\Exception $e) { }
        }

        return false;
    }

}
