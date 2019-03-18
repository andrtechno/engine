<?php

namespace panix\engine\widgets\recaptcha\form;

use Yii;
use panix\engine\blocks_settings\WidgetForm;

class ReCaptchaForm extends WidgetForm {

    public $auth_login;
    public $auth_token;
    public $account;


    public function rules() {
        return [
            [['auth_login', 'auth_token','account'], 'string'],
            [['auth_token','auth_login','account'], 'required'],
        ];
    }

}
