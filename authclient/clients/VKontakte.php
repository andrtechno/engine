<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class VKontakte
 * @package panix\engine\authclient\clients
 */
class VKontakte extends \yii\authclient\clients\VKontakte
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_vkontakte_id) && isset($config->oauth_vkontakte_secret)) {
            $this->clientId = $config->oauth_vkontakte_id;
            $this->clientSecret = $config->oauth_vkontakte_secret;
        }
        parent::init();

    }
}