<?php

namespace panix\engine;

use Yii;

class Mailer extends \yii\swiftmailer\Mailer
{

    public function init()
    {
        $config = Yii::$app->settings->get('app');
        $this->messageConfig['from'] = [$config->email => $config->sitename];

        $transport['class'] = 'Swift_SmtpTransport';
        $transport['host'] = $config->mailer_transport_smtp_host;
        $transport['username'] = $config->mailer_transport_smtp_username;
        $transport['password'] = $config->mailer_transport_smtp_password;
        $transport['port'] = $config->mailer_transport_smtp_port;
        $transport['encryption'] = $config->mailer_transport_smtp_encryption;
        $this->setTransport($transport);

        $this->getView()->renderers = Yii::$app->getView()->renderers;
        parent::init();
    }
}