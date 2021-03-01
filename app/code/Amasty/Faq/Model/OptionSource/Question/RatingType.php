<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\OptionSource\Question;

use Magento\Framework\Option\ArrayInterface;

class RatingType implements ArrayInterface
{
    const YESNO = 0;
    const VOTING = 1;

    public function toOptionArray()
    {
        return [
            ['value' => self::YESNO, 'label'=> __('Yes/No')],
            ['value' => self::VOTING, 'label'=> __('Voting')]
        ];
    }
}
