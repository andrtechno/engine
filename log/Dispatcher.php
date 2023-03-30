<?php

namespace panix\engine\log;

use Yii;

/**
 * Class Dispatcher
 * @package panix\engine\log
 */
class Dispatcher extends \yii\log\Dispatcher
{
    public $enableEmail = false;
    public $traceLevel = YII_DEBUG ? 3 : 0;
    public $flushInterval = 1000 * 10;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $config = Yii::$app->settings->get('logs');
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['error', 'warning'],
            'categories' => ['yii\db\*'],
            'logFile' => 'db_error.log',
        ];
        if (isset($config->query_execute)) {
            $this->targets[] = [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['info'],
                'enabled' => $config->query_execute,
                'categories' => [
                    'yii\db\Command::execute'
                ],
                'logVars' => [],
                'logFile' => 'db_execute.log',

                'except' => [
                    'yii\db\Connection::open',
                    'yii\web\Session::open',
                    'yii\web\Session::close',
                    'yii\web\Session::unfreeze',
                    'yii\web\Session::freeze',
                ],

            ];
        }
        if (isset($config->query_query)) {
            $this->targets[] = [
                'class' => 'panix\engine\log\FileTarget',
                'levels' => ['info'],
                'enabled' => $config->query_query,
                'categories' => [
                    'yii\db\Command::query',
                ],
                'logVars' => [],
                'logFile' => 'db_query.log',

                'except' => [
                    'yii\db\Connection::open',
                    'yii\web\Session::open',
                    'yii\web\Session::close',
                    'yii\web\Session::unfreeze',
                    'yii\web\Session::freeze',
                ],

            ];
        }
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['info'],
            'enabled' => YII_DEBUG,
            'logFile' => 'info.log',
            //'logVars' => [],
            'except' => [
                'yii\db\Command::query',
                'yii\db\Command::execute',
                'yii\db\Connection::open',
                'yii\swiftmailer\Mailer::sendMessage',
                'yii\mail\BaseMailer::send',
                'yii\httpclient\StreamTransport::send',
                'yii\web\Session::open',
                'yii\web\Session::unfreeze',
                'yii\web\Session::freeze',
            ],
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['info'],
            'logVars' => [],
            'logFile' => 'mail.log',
            'categories' => [
                'yii\mail\BaseMailer::send',
            ],

        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['profile'],
            'enabled' => YII_DEBUG,
            'logFile' => 'profile.log',
            'except' => [
                'yii\db\Command::query',
                'yii\db\Command::execute',
                'yii\db\Connection::open',
                'yii\httpclient\StreamTransport::send'
            ],
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['info'],
            'enabled' => YII_DEBUG,
            'logFile' => 'httpclient.log',
            'categories' => [
                'yii\httpclient\StreamTransport::send',
            ],
        ];


        /*[
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['trace'],
            'enabled' => false,
            'logFile' => $logPath . '/trace.log',
        ],*/

        $this->targets[] = [
            'class' => 'panix\engine\log\EmailTarget',
            'levels' => ['error', 'warning'],
            //'categories' => ['yii\base\*'],
            'enabled' => $this->enableEmail,
            'except' => [
                'yii\web\HttpException:404',
                'yii\web\HttpException:403',
                'yii\web\HttpException:400',
                'yii\i18n\PhpMessageSource::loadMessages'
            ],
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['error'],
            'logFile' => 'error.log',
            'except' => [
                'yii\web\HttpException:404',
                'yii\web\HttpException:403',
                'yii\web\HttpException:400',
                'yii\i18n\PhpMessageSource::loadMessages'
            ]
        ];
        $this->targets[] = [
            'class' => 'panix\engine\log\FileTarget',
            'levels' => ['warning'],
            'logFile' => 'warning.log',
        ];

        parent::init();
    }
}
