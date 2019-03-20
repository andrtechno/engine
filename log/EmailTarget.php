<?php

namespace panix\engine\log;

use Yii;

class EmailTarget extends \yii\log\EmailTarget
{
    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->message = [
            'from' => ['log@' . Yii::$app->request->getHostName()],
            'to' => ['dev@pixelion.com.ua'],
            'subject' => 'Ошибки на сайте '.Yii::$app->request->getHostName(),
        ];
        parent::init();
    }
}