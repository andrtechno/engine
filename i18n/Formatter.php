<?php

namespace panix\engine\i18n;
use Yii;
class Formatter extends \yii\i18n\Formatter {

    public function init() {
      //  $this->timeZone = Yii::$app->settings->get('app','timezone');
       // $this->timeZone = Yii::$app->settings->get('app','timezone');
        
           $this->locale = 'ru-RU';
            $this->dateFormat = 'd.MM.Y';
            $this->timeFormat = 'H:mm:ss';
            // 'datetimeFormat' => 'd.MM.Y HH:mm',
            $this->datetimeFormat = 'php:Y-m-d H:i:s';
            //'decimalSeparator' => ',',
            // 'thousandSeparator' => ' ',
            $this->currencyCode = 'UAH';
        
        parent::init();
    }

}
