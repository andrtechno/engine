<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class Google
 * @package panix\engine\authclient\clients
 */
class Google extends \yii\authclient\clients\Google
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_google_id) && isset($config->oauth_google_secret)) {
            $this->clientId = $config->oauth_google_id;
            $this->clientSecret = $config->oauth_google_secret;
        }
        parent::init();

    }
}