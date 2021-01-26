<?php

namespace panix\mod\csv\components;

use Yii;
use yii\queue\RetryableJobInterface;
use yii\base\BaseObject;

class SendEmail extends BaseObject implements RetryableJobInterface
{
    public $params = [];
    public $templatePath;
    public $layoutPath;
    public $recipientEmails = [];
    public $subject;


    public function execute($queue)
    {
        $config = Yii::$app->settings->get('app');
        if(!$this->recipientEmails){
            $this->recipientEmails[]=$config->email;
        }

        $mailer = Yii::$app->mailer;
        $mailer->compose(['html' => $this->templatePath], $this->params)
            ->setFrom(['noreply@example.com' => $config->mailer_sender_name])
            ->setTo($this->recipientEmails)
            ->setSubject($this->subject)
            ->send();


        return true;
    }


    public function getTtr()
    {
        return 20 * 60;
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }
}