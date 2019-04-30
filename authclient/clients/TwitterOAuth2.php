<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class TwitterOAuth2
 * @package panix\engine\authclient\clients
 */
class TwitterOAuth2 extends \yii\authclient\clients\TwitterOAuth2
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_twitter_id) && isset($config->oauth_twitter_secret)) {
            $this->clientId = $config->oauth_twitter_id;
            $this->clientSecret = $config->oauth_twitter_secret;
        }
        parent::init();

    }
}