<?php

namespace panix\engine\log;

use Yii;

/**
 * Class Dispatcher
 * @package panix\engine\log
 */
class Dispatcher extends \yii\log\Dispatcher
{

    public $traceLevel = YII_DEBUG ? 3 : 0;
    public $flushInterval = 1000 * 10;

    /**
     * {@inheritdoc}
     */
    public function init()
    {

        $date = new \DateTime(date('Y-m-d', time()), new \DateTimeZone('Europe/Kiev'));
        $date= $date->format('Y-m-d');
        $logPath = '@runtime/logs/' . $date . '/'.Yii::$app->id;
        $this->targets = [
            [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['error', 'warning'],
                'categories' => ['yii\db\*'],
                'logFile' => $logPath . '/db_error.log',
            ],
            [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['error'],
                'logFile' => $logPath . '/error.log',
            ],
            [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['warning'],
                'logFile' => $logPath . '/warning.log',
            ],
            [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['info'],
                'logFile' => $logPath . '/info.log',
                'except' => [
                    'yii\db\Command::query',
                    'yii\db\Command::execute',
                    'yii\db\Connection::open'
                ],
            ],
            [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['profile'],
                'logFile' => $logPath . '/profile.log',
                'except' => [
                    'yii\db\Command::query',
                    'yii\db\Command::execute',
                    'yii\db\Connection::open'
                ],
            ],
            /*[
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['trace'],
                'enabled' => false,
                'logFile' => $logPath . '/trace.log',
            ],*/
            [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['info'],
                'categories' => ['yii\db\*'],
                'logFile' => $logPath . '/db_info.log',
                'except' => [
                    'yii\db\Connection::open',
                ],

            ],
            [
                'class' => 'panix\engine\log\EmailTarget',
                'levels' => ['error', 'warning'],
                //'categories' => ['yii\base\*'],
                'except' => [
                    'yii\web\HttpException:404',
                    'yii\web\HttpException:403',
                    'yii\web\HttpException:400',
                    'yii\i18n\PhpMessageSource::loadMessages'
                ],
            ],
        ];

        parent::init();


    }
}