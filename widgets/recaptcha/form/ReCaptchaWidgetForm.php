<?php

namespace panix\engine\widgets\recaptcha\form;

use Yii;
use panix\engine\blocks_settings\WidgetFormModel;

class ReCaptchaWidgetForm extends WidgetFormModel {

    public $lat;
    public $lon;
    public $enable_sunrise;
    public $enable_sunset;
    public $enable_humidity;
    public $enable_pressure;
    public $enable_wind;
    public $units;
    public $title;
    public $apikey;

    public function rules() {
        return [
            [['lat', 'lon', 'title', 'units', 'apikey'], 'string'],
            ['apikey', 'required'],
            [['enable_sunrise', 'enable_sunset', 'enable_humidity', 'enable_pressure', 'enable_wind'], 'boolean']
        ];
    }

}
