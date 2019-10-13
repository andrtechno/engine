<?php

namespace panix\engine\widgets\recaptcha;

/**
 * Yii2 Google reCAPTCHA widget global config.
 *
 * @see https://developers.google.com/recaptcha
 * @package panix\engine\widgets\recaptcha
 */
class ReCaptchaConfig
{
    const JS_API_URL_DEFAULT = '//www.google.com/recaptcha/api.js';
    const JS_API_URL_ALTERNATIVE = '//www.recaptcha.net/recaptcha/api.js';

    const SITE_VERIFY_URL_DEFAULT = 'https://www.google.com/recaptcha/api/siteverify';
    const SITE_VERIFY_URL_ALTERNATIVE = 'https://www.recaptcha.net/recaptcha/api/siteverify';

    /** @var string Use [[JS_API_URL_ALTERNATIVE]] when [[JS_API_URL_DEFAULT]] is not accessible. */
    public $jsApiUrl;

    /** @var string Use [[SITE_VERIFY_URL_ALTERNATIVE]] when [[SITE_VERIFY_URL_DEFAULT]] is not accessible. */
    public $siteVerifyUrl;

    /** @var boolean Check host name. */
    public $checkHostName;

    /** @var \yii\httpclient\Request */
    public $httpClientRequest;
}
