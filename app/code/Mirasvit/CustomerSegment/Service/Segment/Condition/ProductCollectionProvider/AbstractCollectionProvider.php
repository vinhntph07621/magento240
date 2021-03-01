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
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Service\Segment\Condition\ProductCollectionProvider;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\Condition\CollectionProviderInterface;
use Mirasvit\CustomerSegment\Model\Segment\Condition\Daterange;

abstract class AbstractCollectionProvider implements CollectionProviderInterface
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $adapter;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * CartProvider constructor.
     *
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
     * @param ProductRepositoryInterface  $productRepository
     * @param ResourceConnection          $resourceConnection
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->adapter = $this->resourceConnection->getConnection();
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get product IDs for candidate by concrete provider.
     * 'product_id' as a column should be specified in first order.
     *
     * @param AbstractModel $candidate
     *
     * @return \Magento\Framework\DB\Select
     */
    abstract protected function getCollectionSelect(AbstractModel $candidate);

    /**
     * Get table date field, which is used to filter collection by date range
     *
     * @return string
     */
    abstract protected function getDateField();

    /**
     * Method used to check whether concrete provider can process this candidate.
     * Some product collection providers work only with registered customers, another with both.
     *
     * @param AbstractModel $candidate
     *
     * @return bool
     */
    public function canProcessCandidate(AbstractModel $candidate)
    {
        return true;
    }

    /**
     * @param AbstractModel $candidate
     *
     * @inheritDoc
     */
    public function provideCollection(AbstractModel $candidate, Daterange $dateRange = null)
    {
        if (!$this->canProcessCandidate($candidate)) {
            return [];
        }

        \Magento\Framework\Profiler::start(get_class($this) . '::provideCollection()');
        $select = $this->getCollectionSelect($candidate);
        if ($dateRange) {
            $dateRange->limitByDateRange($select, $this->getDateField());
        }

        $this->searchCriteriaBuilder->addFilter('entity_id', $this->adapter->fetchCol($select), 'in');
        $items = $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        \Magento\Framework\Profiler::stop(get_class($this) . '::provideCollection()');

        return $items;
    }

    /**
     * Add filter by customer_id or customer_email to select, depending on customer type Registered or Guest.
     *
     * @param Select                           $select
     * @param AbstractModel $candidate
     * @param string                           $customerIdField    - customer_id field
     * @param string                           $customerEmailField - customer_email field
     *
     * @return Select
     */
    protected function filterByCustomer(Select $select, AbstractModel $candidate, $customerIdField, $customerEmailField)
    {
        $whereField = ($candidate->getCustomerId()) ? $customerIdField : $customerEmailField;
        $whereValue = ($candidate->getCustomerId()) ? $candidate->getCustomerId() : $candidate->getEmail();
        $select->where("{$whereField} = ?", $whereValue);

        return $select;
    }
}
