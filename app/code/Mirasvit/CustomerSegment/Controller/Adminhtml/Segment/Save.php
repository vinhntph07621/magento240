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



namespace Mirasvit\CustomerSegment\Controller\Adminhtml\Segment;

use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Controller\Adminhtml\SegmentAbstract;

class Save extends SegmentAbstract
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam(SegmentInterface::ID);

        $model = $this->initModel();

        $data = $this->getRequest()->getParams();

        $data = $this->filter($data, $model);

        if ($data) {
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This segment no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setTitle($data[SegmentInterface::TITLE])
                ->setDescription($data[SegmentInterface::DESCRIPTION])
                ->setType($data[SegmentInterface::TYPE])
                ->setWebsiteId($data[SegmentInterface::WEBSITE_ID])
                ->setPriority($data[SegmentInterface::PRIORITY])
                ->setIsManual($data[SegmentInterface::IS_MANUAL])
                ->setToGroupId($data[SegmentInterface::TO_GROUP_ID])
                ->setStatus($data[SegmentInterface::STATUS])
                ->setConditionsSerialized($data[SegmentInterface::CONDITIONS_SERIALIZED]);

            try {
                $this->segmentRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You have saved the segment.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [SegmentInterface::ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [SegmentInterface::ID => $model->getId()]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array            $data
     * @param SegmentInterface $segment
     *
     * @return array
     */
    private function filter(array $data, SegmentInterface $segment)
    {
        $rule = $segment->getRule();

        $conditions = $segment->getRule()->getConditions()->asArray();

        if (isset($data['rule']) && isset($data['rule']['conditions'])) {
            $rule->loadPost(['conditions' => $data['rule']['conditions']]);

            $conditions = $rule->getConditions()->asArray();
        }

        if (CompatibilityService::is21()) {
            /** mp comment start */
            $conditions = serialize($conditions);
            /** mp comment end */
            /** mp uncomment start
            $conditions = 'a:0:{}';
            mp uncomment end */
        } else {
            $conditions = \Zend_Json::encode($conditions);
        }

        $data[SegmentInterface::CONDITIONS_SERIALIZED] = $conditions;

        if (!isset($data[SegmentInterface::PRIORITY]) || !$data[SegmentInterface::PRIORITY]) {
            $data[SegmentInterface::PRIORITY] = 1;
        }

        if (!isset($data[SegmentInterface::TO_GROUP_ID])) {
            $data[SegmentInterface::TO_GROUP_ID] = '';
        }


        return $data;
    }
}