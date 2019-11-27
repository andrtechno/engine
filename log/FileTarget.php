<?php

namespace panix\engine\log;

use Yii;
use yii\log\FileTarget as BaseFileTarget;
use yii\log\Logger;

/**
 * Class FileTarget
 * @package panix\engine\log
 */
class FileTarget extends BaseFileTarget
{
    public $logVars = [];

    public function init()
    {


        // $fileName = implode('_', $this->getLevels());
//echo implode('_', $this->getLevels());
        //  $this->logFile = '@runtime/logs/' . DATE_LOG . '/db_error.log';
        parent::init();


    }
}