<?php

namespace panix\engine\widgets\recaptcha\form;

use Yii;
use panix\engine\blocks_settings\WidgetModel;

class ReCaptchaForm extends WidgetModel
{

    public $auth_login;
    public $auth_token;
    public $account;


    public function rules()
    {
        return [
            [['auth_login', 'auth_token', 'account'], 'string'],
            [['auth_token', 'auth_login', 'account'], 'required'],
        ];
    }

}
