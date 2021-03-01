<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-sphinx
 * @version   1.1.56
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchSphinx\Adapter;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder as MysqlAggregationBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Search\Adapter\Mysql\ResponseFactory as MysqlResponseFactory;
use Magento\Framework\Search\Adapter\Mysql\DocumentFactory as MysqlDocumentFactory;
use Magento\Framework\App\ObjectManager;
use Mirasvit\Search\Model\Config as SearchConfig;
use Magento\Framework\DB\Select;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Adapter implements AdapterInterface
{
    /**
     * Mapper instance
     *
     * @var Mapper
     */
    protected $mapper;

    /**
     * Response Factory
     *
     * @var MysqlResponseFactory
     */
    protected $responseFactory;

    /**
     * @var MysqlAggregationBuilder
     */
    private $aggregationBuilder;

    /**
     * @var TemporaryStorageFactory
     */
    protected $temporaryStorageFactory;

    /**
     * @var SearchConfig
     */
    protected $searchConfig;

    /**
     * @var array
     */
    private $countSqlSkipParts = [
        \Magento\Framework\DB\Select::LIMIT_COUNT => true,
        \Magento\Framework\DB\Select::LIMIT_OFFSET => true,
    ];
    /**
     * @var ResourceConnection
     */
    private $resource;
    /**
     * @var MysqlDocumentFactory
     */
    private $documentFactory;

    /**
     * Adapter constructor.
     * @param Mapper $mapper
     * @param MysqlResponseFactory $responseFactory
     * @param MysqlAggregationBuilder $aggregationBuilder
     * @param TemporaryStorageFactory $temporaryStorageFactory
     * @param SearchConfig $searchConfig
     * @param MysqlDocumentFactory $documentFactory
     * @param ResourceConnection $resource
     */
    public function __construct(
        Mapper $mapper,
        MysqlResponseFactory $responseFactory,
        MysqlAggregationBuilder $aggregationBuilder,
        TemporaryStorageFactory $temporaryStorageFactory,
        SearchConfig $searchConfig,
        MysqlDocumentFactory $documentFactory,
        ResourceConnection $resource
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->searchConfig = $searchConfig;
        $this->documentFactory = $documentFactory;
        $this->resource = $resource;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    public function query(RequestInterface $request)
    {
        if ($request->getName() == 'catalog_view_container') {
            return $this->fallbackEngine($request);
        }

        try {
            $query = $this->mapper->buildQuery($request);
            $query->limit($this->searchConfig->getResultsLimit());
        } catch (\Exception $e) {
            return $this->fallbackEngine($request);
        }

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeDocumentsFromSelect($query);

        return $this->responseFactory->create([
            'documents'    => $this->getDocuments($table),
            'aggregations' => $this->aggregationBuilder->build($request, $table),
            'total' => $this->getSize($query)
        ]);
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    private function fallbackEngine(RequestInterface $request)
    {
        $objectManager = ObjectManager::getInstance();

        return $objectManager->create('Mirasvit\SearchMysql\Adapter\Adapter')
            ->query($request);
    }

    /**
     * @param \Magento\Framework\DB\Ddl\Table $table
     * @return array
     * @throws \Zend_Db_Exception
     */
    private function getDocuments(\Magento\Framework\DB\Ddl\Table $table)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from($table->getName(), ['entity_id', 'score']);

        return $connection->fetchAssoc($select);
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }

     /**
      * Get rows size
      *
      * @param Select $query
      * @return int
      */
    private function getSize(Select $query)
    {
        $sql = $this->getSelectCountSql($query);
        $parentSelect = $this->getConnection()->select();
        $parentSelect->from(['core_select' => $sql]);
        $parentSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $parentSelect->columns('COUNT(*)');
        $totalRecords = $this->getConnection()->fetchOne($parentSelect);

        return (int)$totalRecords;
    }

    /**
     * Reset limit and offset
     *
     * @param Select $query
     * @return Select
     */
    private function getSelectCountSql(Select $query)
    {
        foreach ($this->countSqlSkipParts as $part => $toSkip) {
            if ($toSkip) {
                $query->reset($part);
            }
        }

        return $query;
    }
}
