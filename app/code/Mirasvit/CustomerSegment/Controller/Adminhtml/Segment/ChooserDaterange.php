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


use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class ChooserDaterange extends Action
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        /** @var \Mirasvit\CustomerSegment\Block\Adminhtml\Segment\Widget\Chooser\Daterange $dateRangeBlock */
        $dateRangeBlock = $this->_view->getLayout()
            ->createBlock('Mirasvit\CustomerSegment\Block\Adminhtml\Segment\Widget\Chooser\Daterange');

        $dateRangeBlock->setTargetElementId($this->getRequest()->getParam('value_element_id'));
        $selectedValues = $this->getRequest()->getParam('selected');
        if (!empty($selectedValues) && is_array($selectedValues) && 1 === count($selectedValues)) {
            $dateRangeBlock->setRangeValue(array_shift($selectedValues));
        }

        return $resultRaw->setContents($dateRangeBlock->toHtml());
    }
}