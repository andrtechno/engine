<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class Live
 * @package panix\engine\authclient\clients
 */
class Live extends \yii\authclient\clients\Live
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_live_id) && isset($config->oauth_live_secret)) {
            $this->clientId = $config->oauth_live_id;
            $this->clientSecret = $config->oauth_live_secret;
        }
        parent::init();

    }
}