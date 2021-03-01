<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Index;

use Amasty\Faq\Model\OptionSource\Question;
use Amasty\Faq\Model\QuestionRepository;
use Amasty\Faq\Model\Voting;
use Magento\Framework\App\Action\Context;

class Vote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var QuestionRepository
     */
    private $repository;

    /**
     * @var Voting
     */
    private $voting;

    public function __construct(
        Context $context,
        QuestionRepository $repository,
        Voting $voting
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->voting = $voting;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        $questionId = (int)$this->_request->getParam('id');

        if ($questionId && $question = $this->getQuestion($questionId)) {
            if ($this->voting->isVotedQuestion($questionId)) {

                return $resultJson->setData(['result' => [
                    'code' => 'already-voted',
                    'message' => __('You already voted')
                ]]);
            }
            try {
                $this->saveVote($question);

                return $resultJson->setData(['result' => [
                    'code' => 'success',
                    'message' => __('You successfully voted')
                ]]);
            } catch (\Exception $e) {

                return $resultJson->setData(['result' => [
                    'code' => 'error',
                    'message' => __('Cant\' save question')
                ]]);
            }
        }

        return $resultJson->setData(['result' => [
            'code' => 'unknown-question',
            'message' => __('Question doesn\'t exists.')
        ]]);
    }

    /**
     * @param int $questionId
     *
     * @return \Amasty\Faq\Api\Data\QuestionInterface|bool|mixed
     */
    private function getQuestion($questionId)
    {
        $question = $this->repository->getById($questionId);
        if ($question->getStatus() == Question\Status::STATUS_ANSWERED
            && $question->getVisibility() != Question\Visibility::VISIBILITY_NONE
        ) {
            return $question;
        }

        return false;
    }

    /**
     * @param \Amasty\Faq\Api\Data\QuestionInterface $question
     */
    private function saveVote(\Amasty\Faq\Api\Data\QuestionInterface $question)
    {
        if (!empty($this->_request->getParam('positive'))) {
            $question->setPositiveRating($question->getPositiveRating() + 1);
            $this->voting->setVotedQuestion($question->getQuestionId());
        } else {
            $question->setNegativeRating($question->getNegativeRating() + 1);
            $this->voting->setVotedQuestion($question->getQuestionId(), false);
        }

        $this->repository->save($question);
    }
}
