<?php

namespace panix\engine;

use Yii;

class Mailer extends \yii\swiftmailer\Mailer
{

    public function init()
    {
        $config = Yii::$app->settings->get('app');
        $this->messageConfig['from'] = [$config->email => $config->sitename];

        if (isset($config->mailer_transport_smtp_enabled) && $config->mailer_transport_smtp_enabled) {
            $transport['class'] = 'Swift_SmtpTransport';
            $transport['host'] = isset($config->mailer_transport_smtp_host) ? $config->mailer_transport_smtp_host : 'localhost';
            $transport['username'] = isset($config->mailer_transport_smtp_username) ? $config->mailer_transport_smtp_username : '';
            $transport['password'] = isset($config->mailer_transport_smtp_password) ? $config->mailer_transport_smtp_password : '';
            $transport['port'] = isset($config->mailer_transport_smtp_port) ? $config->mailer_transport_smtp_port : '';
            $transport['encryption'] = isset($config->mailer_transport_smtp_encryption) ? $config->mailer_transport_smtp_encryption : '';
            $this->setTransport($transport);
        }
        $this->getView()->renderers = Yii::$app->getView()->renderers;
        parent::init();
    }
}