<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class Github
 * @package panix\engine\authclient\clients
 */
class Github extends \yii\authclient\clients\Github
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_github_id) && isset($config->oauth_github_secret)) {
            $this->clientId = $config->oauth_github_id;
            $this->clientSecret = $config->oauth_github_secret;
        }
        parent::init();

    }
}