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



namespace Mirasvit\CustomerSegment\Ui\Segment\Form;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\AddressFactory as AddressFactory;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Service\SegmentService;

class GuestDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var SegmentService
     */
    private $segmentService;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * GuestDataProvider constructor.
     * @param SegmentService $segmentService
     * @param AddressFactory $addressFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        SegmentService $segmentService,
        AddressFactory $addressFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->segmentService = $segmentService;
        $this->addressFactory = $addressFactory;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    /**
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function getSearchCriteria()
    {
        /** @var \Zend\Stdlib\Parameters $query */
        $query = $this->request->getQuery();

        $segmentId = $query->get(SegmentInterface::ID);
        $customers = $this->segmentService->getCustomers($segmentId)
            ->addFieldToFilter(CustomerInterface::CUSTOMER_ID, ['null' => true]);

        $ids = [0];
        /** @var \Mirasvit\CustomerSegment\Model\Segment\Customer $customer */
        foreach ($customers as $customer) {
            $ids[] = intval($customer->getId());
        }

        $filter = new \Magento\Framework\Api\Filter();
        $filter->setField('segment_customer_id')
            ->setConditionType('in')
            ->setValue($ids);

        $this->addFilter($filter);

        return parent::getSearchCriteria();
    }

    /**
     * @return \Magento\Framework\Api\Search\SearchResultInterface
     */
    public function getSearchResult()
    {
        $searchResult = parent::getSearchResult();

        foreach ($searchResult->getItems() as $item) {
            $address = $this->addressFactory->create()
                ->load($item->getCustomAttribute('billing_address_id')->getValue());

            $item->setCustomAttribute('entity_id', 0);
            $item->setCustomAttribute('name', $address->getName());

            foreach ($address->getData() as $key => $value) {
                $item->setCustomAttribute('billing_' . $key, $value);
            }
        }

        return $searchResult;
    }

    /**
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
    }
}
