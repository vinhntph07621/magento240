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




namespace Mirasvit\CsNewsletter\Plugin\Adminhtml\Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form;
use Mirasvit\CsNewsletter\Api\Repository\SegmentNewsletterRepositoryInterface;
use Mirasvit\CustomerSegment\Model\Config\Source\Segment as SegmentSource;

class AddSegmentFilterBlockPlugin
{
    /**
     * @var SegmentSource
     */
    private $segmentSource;
    /**
     * @var SegmentNewsletterRepositoryInterface
     */
    private $segmentNewsletterRepository;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * AddSegmentFilterBlockPlugin constructor.
     * @param RequestInterface $request
     * @param SegmentSource $segmentSource
     * @param SegmentNewsletterRepositoryInterface $segmentNewsletterRepository
     */
    public function __construct(
        RequestInterface $request,
        SegmentSource $segmentSource,
        SegmentNewsletterRepositoryInterface $segmentNewsletterRepository
    ) {
        $this->request = $request;
        $this->segmentSource = $segmentSource;
        $this->segmentNewsletterRepository = $segmentNewsletterRepository;
    }

    /**
     * Add segment filter to newsletter queue edit form.
     *
     * @param Form $subject
     */
    public function beforeGetFormHtml(Form $subject)
    {
        if (is_object($subject->getForm())) {
            $segments = $this->segmentSource->toOptionArray();
            if (count($segments)) {
                $usedSegments = $this->getUsedSegments();
                /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
                $fieldset = $subject->getForm()->getElement('base_fieldset');
                $fieldset->addField('segments', 'multiselect', [
                    'name'   => 'segments[]',
                    'label'  => __('Subscribers From Segments'),
                    'values' => $segments,
                    'value'  => $usedSegments
                ], 'stores');
            }
        }
    }

    /**
     * Get segments associated with current newsletter queue.
     *
     * @return int[]|array
     */
    private function getUsedSegments()
    {
        $segments = [];
        if ($queueId = $this->request->getParam('id')) {
            $segments = $this->segmentNewsletterRepository->getByQueue($queueId);
        }

        return $segments;
    }
}
