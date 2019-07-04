<?php

namespace panix\engine\web;


use Yii;
use yii\web\DbSession as Session;
use panix\engine\CMS;

class DbSession extends Session
{

    public $writeCallback = ['panix\engine\web\DbSession', 'writeFields'];
    public $sessionTable = '{{%session}}';


    public static function writeFields($session)
    {

        //try {
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
                'user_name' => $uname,
                //'is_trusted' => $session->get('is_trusted', false),
            ];

        //} catch (InvalidConfigException $excp) {
        //    \Yii::info(print_r($excp));
        //}
    }

}
