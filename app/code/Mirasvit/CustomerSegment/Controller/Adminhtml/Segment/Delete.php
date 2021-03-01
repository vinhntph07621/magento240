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

use Mirasvit\CustomerSegment\Controller\Adminhtml\SegmentAbstract;

class Delete extends SegmentAbstract
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $model = $this->initModel();

        try {
            $this->segmentRepository->delete($model);
            $this->messageManager->addSuccessMessage(__('The segment has been successfully deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
