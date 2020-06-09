<?php

namespace panix\engine\web;


use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\web\DbSession as BaseDbSession;
use panix\engine\CMS;

/**
 * Class DbSession
 * @package panix\engine\web
 */
class DbSession extends BaseDbSession
{

    public $writeCallback = ['panix\engine\web\DbSession', 'writeFields'];

    public static function writeFields($session)
    {

        try {
            $uid = (Yii::$app->user->getIdentity(false) == null) ? null : Yii::$app->user->getIdentity(false)->id;
            $ip = (Yii::$app->id !== 'console')?Yii::$app->request->getRemoteIP():NULL;
            if (Yii::$app->user->getIsGuest()) {
                $checkBot = (Yii::$app->id !== 'console')?CMS::isBot():false;
                if ($checkBot['success']) {
                    $user_name = substr($checkBot['name'], 0, 25);
                    $user_type = 'SearchBot';
                } else {
                    $user_name = $ip;
                    $user_type = 'Guest';
                }
            } else {
                $user_type = 'User';
                $user_name = Yii::$app->user->username;
            }


            $data = [];
            $data['user_id'] = $uid;
            $data['ip'] = $ip;
            $data['user_agent'] = (Yii::$app->id !== 'console')?Yii::$app->request->getUserAgent():null;
            $data['user_type'] = $user_type;
            $data['user_name'] = $user_name;
            return $data;

        } catch (InvalidConfigException $e) {
            echo 'session error panix\engine\web\DbSession';
            \Yii::info(print_r($e));
        }
    }


}
