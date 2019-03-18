<?php

namespace panix\engine\web;

use panix\engine\CMS;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\DbSession as Session;

class DbSession extends Session
{

    public $writeCallback = ['panix\engine\web\DbSession', 'writeFields'];
    public $sessionTable = '{{%session}}';


    public static function writeFields()
    {

        try {
            $uid = (Yii::$app->user->getIdentity(false) == null) ? null : Yii::$app->user->getIdentity(false)->id;
            $ip = $_SERVER['REMOTE_ADDR'];
            if (Yii::$app->user->getIsGuest()) {
                $checkBot = CMS::isBot();
                if ($checkBot['success']) {
                    $uname = substr($checkBot['name'], 0, 25);
                    $user_type = 'SearchBot';
                } else {
                    $uname = $ip;
                    $user_type = 'Guest';
                }
            } else {
                $user_type = 'User';
                $uname = Yii::$app->user->username;
            }

            return [
                'user_id' => $uid,
                'ip' => $ip,
                'expire_start' => time(),
                'user_type' => $user_type,
                'user_name' => $uname
            ];

        } catch (InvalidConfigException $excp) {
            \Yii::info(print_r($excp));
        }
    }

}