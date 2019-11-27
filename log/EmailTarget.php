<?php

namespace panix\engine\log;

use Yii;
use yii\log\EmailTarget as BaseEmailTarget;

/**
 * Class EmailTarget
 * @package panix\engine\log
 */
class EmailTarget extends BaseEmailTarget
{
    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->message['to'] = ['dev@pixelion.com.ua'];
        if (Yii::$app->id == 'console') {
            $this->message['from'] = ['log_console@pixelion.com.ua'];
            $this->message['subject'] = 'Ошибки на сайте';
        } else {
            $this->message['from'] = ['log@' . Yii::$app->request->getHostName()];
            $this->message['subject'] = 'Ошибки на сайте ' . Yii::$app->request->getHostName();
        }
        parent::init();
    }
}