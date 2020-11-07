<?php

namespace panix\engine\web;

use yii\web\GroupUrlRule as YiiGroupUrlRule;

class GroupUrlRule extends YiiGroupUrlRule
{
    public $ruleConfig = ['class' => 'panix\engine\web\UrlRule'];

}