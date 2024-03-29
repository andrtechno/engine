<?php

namespace panix\engine\behaviors;

use yii\base\Behavior;
use panix\mod\admin\models\Notification;

class NotificationBehavior extends Behavior
{

    public $type = 'info';
    public $text;
    public $sound = null;
    public $url = null;

    public function attach($owner)
    {
        parent::attach($owner);
        $notification = new Notification;
        $notification->type = $this->type;
        $notification->text = $this->text;
        $notification->sound = $this->sound;
        $notification->url = $this->url;
        $notification->save(false);
    }

}
