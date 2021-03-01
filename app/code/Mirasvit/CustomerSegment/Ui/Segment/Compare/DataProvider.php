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



namespace Mirasvit\CustomerSegment\Ui\Segment\Compare;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\SegmentServiceInterface;
use Mirasvit\ReportApi\Api\RequestBuilderInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var SegmentRepositoryInterface
     */
    private $repository;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var SegmentServiceInterface
     */
    private $segmentService;

    /**
     * @var RequestBuilderInterface
     */
    private $requestBuilder;

    /**
     * @var SchemaInterface
     */
    private $schema;

    /**
     * DataProvider constructor.
     * @param SegmentRepositoryInterface $segmentRepository
     * @param SegmentServiceInterface $segmentService
     * @param RequestBuilderInterface $requestBuilder
     * @param SchemaInterface $schema
     * @param ContextInterface $context
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        SegmentRepositoryInterface $segmentRepository,
        SegmentServiceInterface $segmentService,
        RequestBuilderInterface $requestBuilder,
        SchemaInterface $schema,
        ContextInterface $context,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->repository     = $segmentRepository;
        $this->segmentService = $segmentService;
        $this->collection     = $segmentRepository->getCollection();
        $this->requestBuilder = $requestBuilder;
        $this->schema         = $schema;
        $this->context        = $context;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $segments = $this->getSegments();

        $items = [];
        foreach ($segments as $segment) {
            $data = [
                SegmentInterface::ID    => $segment->getId(),
                SegmentInterface::TITLE => $segment->getTitle(),
                'size'                  => $this->segmentService
                    ->getCustomersCount($segment->getId()),
                'intersection'          => [],
                'reports'               => [],
            ];


            $response = $this->requestBuilder->create()
                ->setTable('sales_order')
                ->setColumns([
                    'sales_order|entity_id__cnt',
                    'sales_order|total_qty_ordered__sum',
                    'sales_order|total_qty_ordered__avg',
                    'sales_order|grand_total__avg',
                    'sales_order|grand_total__sum',
                ])
                ->addFilter(
                    'mst_customersegment_segment|segment_id',
                    $segment->getId()
                )
                ->process();

            foreach ($response->getTotals()->getFormattedData() as $identifier => $value) {
                $column            = $this->schema->getColumn($identifier);
                $data['reports'][] = [
                    'label' => $column->getLabel() . ' / ' . $column->getAggregator()->getLabel(),
                    'value' => $value,
                ];
            }

            $items[] = $data;
        }

        //        echo '<pre>';
        //        print_r($items);
        //        die();

        foreach ($items as &$itemA) {
            foreach ($items as $itemB) {
                if ($itemA[SegmentInterface::ID] === $itemB[SegmentInterface::ID]) {
                    $itemA['intersection'][] = [
                        SegmentInterface::TITLE => $itemB[SegmentInterface::TITLE],
                        'size'                  => '-',
                    ];

                    continue;
                }

                $size = $this->segmentService->getCustomersInterceptionCount(
                    $itemA[SegmentInterface::ID],
                    $itemB[SegmentInterface::ID]
                );

                $itemA['intersection'][] = [
                    SegmentInterface::TITLE => $itemB[SegmentInterface::TITLE],
                    'size'                  => $size,
                ];
            }
        }

        return [
            'items' => $items,
        ];
    }

    /**
     * @return SegmentInterface[]
     */
    private function getSegments()
    {
        $ids = $this->context->getRequestParam($this->getRequestFieldName(), []);

        return $this->repository->getCollection()
            ->addFieldToFilter(SegmentInterface::ID, $ids);
    }
}
