<?php

namespace AppBundle\Utils;

class MomentFormatConverter
{
    /**
     * @var array
     */
    private static $formatConvertRules = [
        //year
        'yyyy' => 'YYYY', 'yy' => 'YY', 'y' => 'YYYY',
        //day
        'dd' => 'DD', 'd' => 'D',
        // day of week
        'EE' => 'ddd', 'EEEEEE' => 'dd',
        // timezone
        'ZZZZZ' => 'Z', 'ZZZ' => 'ZZ',
        // letter 'T'
        '\'T\'' => 'T',
    ];

    /**
     * Returns associated moment.js format
     * 
     * @param string $format PHP Date format
     * 
     * @return string
     */
    public function convert($format)
    {
        return strtr($format, self::$formatConvertRules);
    }
}