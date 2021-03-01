<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\RichData;

use Amasty\Faq\Block\AbstractBlock;

class StructuredData extends AbstractBlock
{
    const BLOCK_NAME = 'amasty_faq_structureddata';
    const FAQ_PAGE = 'FAQPage';
    const QA_PAGE = 'QAPage';

    /**
     * @var string
     */
    protected $_template = 'Amasty_Faq::structured.phtml';

    /**
     * Get structured data
     *
     * @return void|array
     */
    public function getStructuredData()
    {
        $questions = $this->getQuestions();
        $pageType = $this->getData('pageType');
        if (is_array($questions)) {
            $items = [];
            foreach ($questions as $question) {
                if (!empty($question->prepareShortAnswer())) {
                    $items[] = [
                        '@type' => 'Question',
                        'position' => $question->getPosition(),
                        'name' => $question->getTitle(),
                        'answerCount' => 1,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $question->prepareShortAnswer()
                        ]
                    ];
                }
            }

            if (!empty($items)) {
                return [
                    '@context' => 'http://schema.org',
                    '@type' => $pageType,
                    'speakable' => [
                        '@type' => 'SpeakableSpecification',
                        'xPath' => ['/html/head/title']
                    ],
                    'mainEntity' => $items
                ];
            }
        }
    }

    /**
     * @return array
     */
    public function getQuestions()
    {
        $questions = [];
        if ($this->getData('questions')) {
            $questions = $this->getData('questions');
        } elseif (method_exists($this->getParentBlock(), 'getStructuredDataQuestions')) {
            $questions = $this->getParentBlock()->getStructuredDataQuestions();
        }

        return $questions;
    }
}
