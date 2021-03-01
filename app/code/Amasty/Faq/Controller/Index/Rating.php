<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Index;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Model\ResourceModel\Question\CollectionFactory;
use Amasty\Faq\Model\Voting;
use Magento\Framework\App\Action\Context;

class Rating extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Voting
     */
    private $voting;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Voting $voting
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->voting = $voting;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultItems = [];
        if ($questionIds = $this->_request->getParam('items')) {
            array_walk($questionIds, function (&$questionId) {
                $questionId = (int) $questionId;
            });
            $resultItems = $this->getResultItems($questionIds);
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        return $resultJson->setData($resultItems);
    }

    /**
     * @param int[] $questionIds
     *
     * @return array
     */
    private function getResultItems($questionIds)
    {
        $questionCollection = $this->collectionFactory->create();
        $questionCollection->addFieldToFilter(QuestionInterface::QUESTION_ID, ['in' => $questionIds]);
        $questionCollection->addFieldToSelect([
            QuestionInterface::QUESTION_ID,
            QuestionInterface::POSITIVE_RATING,
            QuestionInterface::NEGATIVE_RATING
        ]);

        $result = [];
        foreach ($questionCollection->getData() as $question) {
            $questionId = $question[QuestionInterface::QUESTION_ID];
            $result[] = [
                'id' => $questionId,
                'positiveRating' => $question[QuestionInterface::POSITIVE_RATING],
                'negativeRating' => $question[QuestionInterface::NEGATIVE_RATING],
                'isVoted' => $this->voting->isVotedQuestion($questionId),
                'isPositiveVoted' => $this->voting->isPositiveVotedQuestion($questionId)
            ];
        }

        return $result;
    }
}
