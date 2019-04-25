<?php

namespace panix\engine\i18n;

use panix\engine\CMS;

/**
 * Class Formatter
 * @package panix\engine\i18n
 */
class Formatter extends \yii\i18n\Formatter
{

    public function init()
    {
        $this->timeZone = CMS::timezone();
        $this->locale = 'ru-RU';
        $this->dateFormat = 'php:d M Y';
        $this->timeFormat = 'php:H:i';
        $this->datetimeFormat = 'php:d M Y в H:i';
        //'decimalSeparator' => ',',
        //'thousandSeparator' => ' ',
        $this->currencyCode = 'UAH';
        parent::init();
    }

}
