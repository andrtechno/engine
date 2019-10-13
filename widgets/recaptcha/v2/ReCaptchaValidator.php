<?php
/**
 * @link https://github.com/himiklab/yii2-recaptcha-widget
 * @copyright Copyright (c) 2014-2019 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace panix\engine\widgets\recaptcha\v2;

use panix\engine\widgets\recaptcha\ReCaptchaBaseValidator;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * ReCaptcha widget validator.
 *
 * @package panix\engine\widgets\recaptcha
 */
class ReCaptchaValidator extends ReCaptchaBaseValidator
{
    /** @var string */
    public $message;

    public function __construct(
        $secret = null,
        $siteVerifyUrl = null,
        $checkHostName = null,
        yii\httpclient\Request $httpClientRequest = null,
        $config = [])
    {
        if ($secret && !$this->secret) {
            $this->secret = $secret;
        }

        parent::__construct($siteVerifyUrl, $checkHostName, $httpClientRequest, $config);
    }

    public function init()
    {
        parent::init();

        if (!$this->secret) {
            if (Yii::$app->settings->get('app','recaptcha_secret')) {
                $this->secret = Yii::$app->settings->get('app','recaptcha_secret');
            } else {
                throw new InvalidConfigException('Required `secret` param isn\'t set.');
            }
        }
    }

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param \yii\web\View $view
     * @return string
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = \addslashes($this->message ?: Yii::t(
            'yii',
            '{attribute} cannot be blank.',
            ['attribute' => $model->getAttributeLabel($attribute)]
        ));

        return <<<JS
if (!value) {
     messages.push("{$message}");
}
JS;
    }

    /**
     * @param string|array $value
     * @return array|null
     * @throws Exception
     * @throws \yii\base\InvalidParamException
     */
    protected function validateValue($value)
    {
        if ($this->isValid === null) {
            if (!$value) {
                $this->isValid = false;
            } else {
                $response = $this->getResponse($value);
                if (!isset($response['success'], $response['hostname']) ||
                    ($this->checkHostName && $response['hostname'] !== $this->getHostName())
                ) {
                    throw new Exception('Invalid recaptcha verify response.');
                }

                $this->isValid = $response['success'] === true;
            }
        }

        return $this->isValid ? null : [$this->message, []];
    }

    protected function getHostName()
    {
        return Yii::$app->request->hostName;
    }
}
