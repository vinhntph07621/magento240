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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\SegmentServiceInterface;

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
     * DataProvider constructor.
     * @param SegmentRepositoryInterface $segmentRepository
     * @param SegmentServiceInterface $segmentService
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
        $this->context        = $context;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        $model = $this->getModel();

        if ($model) {
            $data = $model->getData();

            $result[$model->getId()] = $data;
        }

        return $result;
    }

    /**
     * @return bool|false|\Mirasvit\CustomerSegment\Api\Data\SegmentInterface
     */
    private function getModel()
    {
        $id = $this->context->getRequestParam($this->getRequestFieldName(), null);

        return $id ? $this->repository->get($id) : false;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        $model = $this->getModel();

        if ($model) {
            $countCustomers = $this->segmentService->getCustomers($model->getId())
                ->addFieldToFilter(CustomerInterface::CUSTOMER_ID, ['gt' => 0])
                ->getSize();

            $meta['customers']['arguments']['data']['config']['label'] = __('Matched Customers (%1)', $countCustomers);

            $countGuests = $this->segmentService->getCustomers($model->getId())
                ->addFieldToFilter(CustomerInterface::CUSTOMER_ID, ['null' => true])
                ->getSize();

            $meta['guests']['arguments']['data']['config']['label'] = __('Matched Guests (%1)', $countGuests);
        } else {
            $meta['customers']['arguments']['data']['config']['visible'] = false;
            $meta['history']['arguments']['data']['config']['visible']   = false;
        }

        return $meta;
    }
}
