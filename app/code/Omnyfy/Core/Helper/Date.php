<?php

namespace Omnyfy\Core\Helper;

class Date extends \Magento\Framework\App\Helper\AbstractHelper
{

    const WEEKDAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday'
    ];

    public function convertTime($datetime, $fromTz, $toTz, $format='Y-m-d H:i:s')
    {
        $date = new \DateTime($datetime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        return $date->format($format);
    }
}
