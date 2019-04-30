<?php

namespace panix\engine\authclient\clients;

use Yii;

/**
 * Class LinkedIn
 * @package panix\engine\authclient\clients
 */
class LinkedIn extends \yii\authclient\clients\LinkedIn
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('user');
        if (isset($config->oauth_linkedin_id) && isset($config->oauth_linkedin_secret)) {
            $this->clientId = $config->oauth_linkedin_id;
            $this->clientSecret = $config->oauth_linkedin_secret;
        }
        parent::init();

    }
}