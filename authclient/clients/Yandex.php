<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class Yandex
 * @package panix\engine\authclient\clients
 */
class Yandex extends \yii\authclient\clients\Yandex
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_yandex_id) && isset($config->oauth_yandex_secret)) {
            $this->clientId = $config->oauth_yandex_id;
            $this->clientSecret = $config->oauth_yandex_secret;
        }
        parent::init();

    }
}