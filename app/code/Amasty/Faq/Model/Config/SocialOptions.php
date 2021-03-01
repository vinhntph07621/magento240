<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Config;

use Amasty\Faq\Model\SocialDataList;
use Magento\Framework\Data\OptionSourceInterface;

class SocialOptions implements OptionSourceInterface
{
    /**
     * @var SocialDataList
     */
    private $socialDataList;

    public function __construct(
        SocialDataList $socialDataList
    ) {
        $this->socialDataList = $socialDataList;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->socialDataList->getSocialList() as $socialData) {
            $options[] = ['value' => $socialData->getCode(), 'label'=> __($socialData->getName())];
        }

        return $options;
    }
}
