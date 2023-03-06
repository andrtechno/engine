<?php

namespace panix\engine\api;

use Yii;
use yii\base\ActionFilter;
use yii\helpers\IpHelper;

class IpAccessControl extends ActionFilter
{
    public $ips = ['127.0.0.1', '::1'];

    public function beforeAction($action)
    {
        $allowed = false;

        $ip = Yii::$app->getRequest()->getUserIP();
        foreach ($this->ips as $filter) {
            if ($filter === '*'
                || $filter === $ip
                || (
                    ($pos = strpos($filter, '*')) !== false
                    && !strncmp($ip, $filter, $pos)
                )
                || (
                    strpos($filter, '/') !== false
                    && IpHelper::inRange($ip, $filter)
                )
            ) {
                $allowed = true;
                break;
            }
        }

        if ($allowed === false) {
            return false;
        }
        return true;
    }

}