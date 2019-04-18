<?php

namespace panix\engine\behaviors;

use Yii;
use yii\base\Behavior;
use panix\mod\admin\models\Notifications;

class NotifactionBehavior extends Behavior {

    public $type = 'info';
    public $text;

    public function attach($owner) {
        parent::attach($owner);
        $notifaction = new Notifications;
        $notifaction->type = $this->type;
        $notifaction->text = $this->text;
        $notifaction->save(false);
    }

}
