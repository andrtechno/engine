<?php

namespace panix\engine\web;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\base\InvalidConfigException;
use yii\di\Instance;

class DbUserSession extends \yii\web\DbSession {

    public $writeCallback = ['panix\engine\web\DbUserSession', 'writeFields'];

    public static function writeFields($session) {

        try {
            $uid = (Yii::$app->user->getIdentity(false) == null) ? null : Yii::$app->user->getIdentity(false)->id;

            //$test = \panix\mod\user\models\SessionUser::findOne($uid);
            //if($test){
                
            //}
            return [
                'user_id' => $uid,
                'ip' => $_SERVER['REMOTE_ADDR']
            ];
        } catch (Exception $excp) {
            \Yii::info(print_r($excp), 'informazioni');
        }
    }

}
