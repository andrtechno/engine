<?php

namespace panix\engine\i18n;

class Formatter extends \yii\i18n\Formatter
{

    public function init()
    {
        //  $this->timeZone = Yii::$app->settings->get('app','timezone');
        // $this->timeZone = Yii::$app->settings->get('app','timezone');

        $this->locale = 'ru-RU';
        $this->dateFormat = 'php:d M Y';
        $this->timeFormat = 'php:H:i';
        // 'datetimeFormat' => 'd.MM.Y HH:mm',
        $this->datetimeFormat = 'php:d M Y H:i';
        //'decimalSeparator' => ',',
        // 'thousandSeparator' => ' ',
        $this->currencyCode = 'UAH';

        parent::init();
    }

}
