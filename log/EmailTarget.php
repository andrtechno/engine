<?php

namespace panix\engine\log;

use Yii;
use yii\log\EmailTarget as BaseEmailTarget;

/**
 * Class EmailTarget
 * @package panix\engine\log
 */
class EmailTarget extends BaseEmailTarget
{

    public $maskVars = [
        '_SERVER.HTTP_AUTHORIZATION',
        '_SERVER.PHP_AUTH_USER',
        '_SERVER.PHP_AUTH_PW',
        '_SERVER.SCRIPT_NAME',
        '_SERVER.PHP_SELF',
        '_SERVER.REQUEST_TIME_FLOAT',
        '_SERVER.HTTP_CONNECTION',
        '_SERVER.HTTP_CACHE_CONTROL',
        '_SERVER.PATH',
        '_SERVER.SERVER_PROTOCOL',
        '_SERVER.REMOTE_PORT',
        '_SERVER.SERVER_PORT',
        '_SERVER.HTTP_COOKIE',
        '_SERVER.REQUEST_METHOD',
        '_SERVER.SCRIPT_FILENAME',
        '_SERVER.PATHEXT',
        '_SERVER.COMSPEC',
        '_SERVER.REQUEST_SCHEME',
        '_SERVER.SystemRoot',
        '_SERVER.CONTEXT_DOCUMENT_ROOT',
        '_SERVER.GATEWAY_INTERFACE',
        '_SERVER.DOCUMENT_ROOT',
        '_SERVER.WINDIR',
        '_SERVER.SERVER_SOFTWARE',
        '_SERVER.SERVER_ADMIN',
        '_SERVER.REQUEST_TIME',
        '_SERVER.SERVER_NAME',
        '_SERVER.HTTP_CACHE_CONTROL',
        '_SERVER.HTTP_UPGRADE_INSECURE_REQUESTS',
        '_SERVER.HTTP_HOST',
        '_SERVER.REDIRECT_REDIRECT_STATUS',
        '_SERVER.REDIRECT_STATUS',
        '_SERVER.HTTP_ACCEPT_ENCODING',
        '_SERVER.HTTP_UPGRADE_INSECURE_REQUESTS',
        '_SERVER.argv',
        '_SERVER.HTTP_X_REQUESTED_WITH',
        '_SERVER.HTTP_X_CSRF_TOKEN',
        '_SERVER.HTTP_ACCEPT',
        '_SERVER.HTTP_REFERER'
    ];
    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->message['to'] = ['dev@pixelion.com.ua'];
        if (Yii::$app->id == 'console') {
            $this->message['from'] = ['log_console@pixelion.com.ua'];
            $this->message['subject'] = 'Ошибки на сайте';
        } else {
            $this->message['from'] = ['log@' . Yii::$app->request->getHostName()];
            $this->message['subject'] = 'Ошибки на сайте ' . Yii::$app->request->getHostName();
        }
        parent::init();
    }
}