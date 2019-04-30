<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class GoogleHybrid
 * @package panix\engine\authclient\clients
 */
class GoogleHybrid extends \yii\authclient\clients\GoogleHybrid
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        /*$config = Yii::$app->settings->get('user');
        if (isset($config->oauth_twitter_id) && isset($config->oauth_twitter_secret)) {
            $this->clientId = $config->oauth_twitter_id;
            $this->clientSecret = $config->oauth_twitter_secret;
        }*/
        parent::init();

    }
}