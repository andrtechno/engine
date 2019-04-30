<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class Facebook
 * @package panix\engine\authclient\clients
 */
class Facebook extends \yii\authclient\clients\Facebook
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_facebook_id) && isset($config->oauth_facebook_secret)) {
            $this->clientId = $config->oauth_facebook_id;
            $this->clientSecret = $config->oauth_facebook_secret;
        }
        parent::init();

    }
}