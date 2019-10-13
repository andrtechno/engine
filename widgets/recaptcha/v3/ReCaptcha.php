<?php

namespace panix\engine\widgets\recaptcha\v3;

use panix\engine\widgets\recaptcha\ReCaptchaConfig;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Yii2 Google reCAPTCHA v3 widget.
 *
 * For example:
 *
 *```php
 * <?= $form->field($model, 'reCaptcha')->widget(
 *  ReCaptcha3::className(),
 *  [
 *   'key' => 'your key', // unnecessary is reCaptcha component was set up
 *   'threshold' => 0.5,
 *   'action' => 'homepage',
 *  ]
 * ) ?>
 *```
 *
 * or
 *
 *```php
 * <?= ReCaptcha3::widget([
 *  'name' => 'reCaptcha',
 *  'key' => 'your key', // unnecessary is reCaptcha component was set up
 *  'threshold' => 0.5,
 *  'action' => 'homepage',
 *  'widgetOptions' => ['class' => 'col-sm-offset-3'],
 * ]) ?>
 *```
 *
 * @see https://developers.google.com/recaptcha/docs/v3
 * @package panix\engine\widgets\recaptcha
 */
class ReCaptcha extends InputWidget
{
    /** @var string Your key. */
    public $key;

    /**
     * @var string Use [[ReCaptchaConfig::JS_API_URL_ALTERNATIVE]] when [[ReCaptchaConfig::JS_API_URL_DEFAULT]]
     * is not accessible.
     */
    public $jsApiUrl = ReCaptchaConfig::JS_API_URL_DEFAULT;

    /** @var string reCAPTCHA v3 action for this page. */
    public $action;

    /** @var string Your JS callback function that's executed when reCAPTCHA executed. */
    public $jsCallback;

    public function __construct($key = null, $jsApiUrl = null, $config = [])
    {
        if ($key && !$this->key) {
            $this->key = $key;
        }
        if ($jsApiUrl && !$this->jsApiUrl) {
            $this->jsApiUrl = $jsApiUrl;
        }

        parent::__construct($config);
    }

    public function init()
    {

        parent::init();

        if (!$this->key) {
            if (Yii::$app->settings->get('app','recaptcha_key')) {
                $this->key = Yii::$app->settings->get('app','recaptcha_key');
            } else {
                throw new InvalidConfigException('Required `key` param isn\'t set.');
            }
        }
        if (!$this->jsApiUrl) {
                $this->jsApiUrl = ReCaptchaConfig::JS_API_URL_DEFAULT;
        }
        if (!$this->action) {
            $this->action = \preg_replace('/[^a-zA-Z\d\/]/', '', \urldecode(Yii::$app->request->url));
        }

    }

    public function run()
    {
        parent::run();
        $view = $this->view;

        $arguments = \http_build_query([
            'render' => $this->key,
           // 'data-theme'=>'light'
        ]);


        $view->registerJsFile(
            $this->jsApiUrl . '?' . $arguments,
            ['position' => $view::POS_END]
        );
        $view->registerJs(
            <<<JS
"use strict";
grecaptcha.ready(function() {
    grecaptcha.execute("{$this->key}", {action: "{$this->action}"}).then(function(token) {
       // jQuery("#" + "{$this->getReCaptchaId()}").val(token);
        jQuery("#{$this->getReCaptchaId()}").val(token);

        const jsCallback = "{$this->jsCallback}";
        if (jsCallback) {
            eval("(" + jsCallback + ")(token)");
        }
    });
});
JS
            , $view::POS_READY);

        $this->customFieldPrepare();
    }

    protected function customFieldPrepare()
    {
        if ($this->hasModel()) {
            $inputName = Html::getInputName($this->model, $this->attribute);
        } else {
            $inputName = $this->name;
        }

        $options = $this->options;
        $options['id'] = $this->getReCaptchaId();
        echo Html::input('hidden', $inputName, null, $options);
    }

    protected function getReCaptchaId()
    {
        if (isset($this->options['id'])) {
            return $this->options['id'];
        }

        if ($this->hasModel()) {
            return Html::getInputId($this->model, $this->attribute);
        }

        return $this->id . '-' . $this->inputNameToId($this->name);
    }

    protected function inputNameToId($name)
    {
        return \str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], \strtolower($name));
    }

}
