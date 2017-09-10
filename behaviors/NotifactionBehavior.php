<?php

namespace panix\engine\behaviors;

use Yii;
use yii\db\ActiveRecord;
use panix\mod\admin\models\Notifactions;

class NotifactionBehavior extends \yii\base\Behavior {

    public $type = 'info';
    public $text;

    public function attach($owner) {
        parent::attach($owner);
        $notifaction = new Notifactions;
        $notifaction->type = $this->type;
        $notifaction->text = $this->text;
        $notifaction->save(false);
    }

}
