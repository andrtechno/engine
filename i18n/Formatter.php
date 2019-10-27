<?php

namespace panix\engine\i18n;

use panix\engine\CMS;
use panix\engine\Html;
use panix\engine\components\Browser;

/**
 * Class Formatter
 * @package panix\engine\i18n
 * @use \yii\i18n\Formatter
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

    public function asIp($value, $options = [])
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return Html::a(CMS::ip($value), ['/'], $options);
    }

    public function asUserAgent($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $browser = new Browser($value);
        return $browser->getPlatformIcon() . ' ' . $browser->getBrowserIcon() . ' ' . Html::icon('info', ['title' => $value]);
    }
}
