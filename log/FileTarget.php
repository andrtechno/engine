<?php

namespace panix\engine\log;

use Yii;
use yii\helpers\VarDumper;
use yii\log\FileTarget as BaseFileTarget;
use yii\log\Logger;
use yii\web\Request;
use ZipArchive;

/**
 * Class FileTarget
 * @package panix\engine\log
 */
class FileTarget extends BaseFileTarget
{
    public $logVars = [
        // '_POST',
        '_SERVER'
    ];
    public $maskVars = [
        '_SERVER.HTTP_AUTHORIZATION',
        '_SERVER.PHP_AUTH_USER',
        '_SERVER.PHP_AUTH_PW',
        '_SERVER.SCRIPT_NAME',
        '_SERVER.PHP_SELF',
        '_SERVER.REQUEST_TIME_FLOAT',
        '_SERVER.HTTP_CONNECTION',
        '_SERVER.HTTP_CACHE_CONTROL',
        '_SERVER.PATH',
        '_SERVER.SERVER_PROTOCOL',
        '_SERVER.REMOTE_PORT',
        '_SERVER.SERVER_PORT',
        '_SERVER.HTTP_COOKIE',
        '_SERVER.REQUEST_METHOD',
        '_SERVER.SCRIPT_FILENAME',
        '_SERVER.PATHEXT',
        '_SERVER.COMSPEC',
        '_SERVER.REQUEST_SCHEME',
        '_SERVER.SystemRoot',
        '_SERVER.CONTEXT_DOCUMENT_ROOT',
        '_SERVER.GATEWAY_INTERFACE',
        '_SERVER.DOCUMENT_ROOT',
        '_SERVER.WINDIR',
        '_SERVER.SERVER_SOFTWARE',
        '_SERVER.SERVER_ADMIN',
        '_SERVER.REQUEST_TIME',
        '_SERVER.SERVER_NAME',
        '_SERVER.HTTP_CACHE_CONTROL',
        '_SERVER.HTTP_UPGRADE_INSECURE_REQUESTS',
        '_SERVER.HTTP_HOST',
        '_SERVER.REDIRECT_REDIRECT_STATUS',
        '_SERVER.REDIRECT_STATUS',
        '_SERVER.HTTP_ACCEPT_ENCODING',
        '_SERVER.HTTP_UPGRADE_INSECURE_REQUESTS',
        '_SERVER.argv',
        '_SERVER.HTTP_X_REQUESTED_WITH',
        '_SERVER.HTTP_X_CSRF_TOKEN',
        '_SERVER.HTTP_ACCEPT',
        '_SERVER.HTTP_REFERER'
    ];

    public function init()
    {
        if (strpos($this->logFile, '@') === false) {
            $date = new \DateTime(date('Y-m-d', time()), new \DateTimeZone('Europe/Kiev'));
            $date = $date->format('Y-m-d');
            $this->logFile = '@runtime/logs/' . $date . '/' . Yii::$app->id . '/' . $this->logFile;
        }
        parent::init();
        if (Yii::$app->id != 'console') {
            if (Yii::$app->request->isPjax || Yii::$app->request->isPjax) {
                $this->setEnabled(false);
            }
        }
    }

    public function formatMessage($message)
    {
        return '#>' . parent::formatMessage($message);
    }

    public function getMessagePrefix($message)
    {
        if ($this->prefix !== null) {
            return call_user_func($this->prefix, $message);
        }

        if (Yii::$app === null) {
            return '';
        }

        $request = Yii::$app->getRequest();
        $ip = $request instanceof Request ? $request->getUserIP() : '-';

        /* @var $user \yii\web\User */
        $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
        if ($user && ($identity = $user->getIdentity(false))) {
            $userID = $identity->getId();
            $userEmail = $user->getEmail();
        } else {
            $userID = '-';
            $userEmail = '-';
        }

        return "[$ip][$userID][$userEmail]";
    }

    protected function rotateFiles()
    {

        $file = $this->logFile;

        for ($i = $this->maxLogFiles; $i >= 0; --$i) {
            // $i == 0 is the original log file
            $rotateFile = $file . ($i === 0 ? '' : '.' . $i);
            if (is_file($rotateFile)) {
                // suppress errors because it's possible multiple processes enter into this section
                if ($i === $this->maxLogFiles) {
                    @unlink($rotateFile);
                    continue;
                }
                $newFile = $newFile = $this->logFile . '.' . time();

                $zip = new ZipArchive();
                $zip->open(dirname($this->logFile) . DIRECTORY_SEPARATOR . basename($this->logFile) . '.zip', ZipArchive::CREATE);
                $zip->addFile($this->logFile, basename($newFile));
                $zip->close();
                @unlink($newFile);

                if ($i === 0) {
                    $this->clearLogFile($rotateFile);
                }
            }
        }
    }

    /***
     * Clear log file without closing any other process open handles
     * @param string $rotateFile
     */
    private function clearLogFile($rotateFile)
    {
        if ($filePointer = @fopen($rotateFile, 'a')) {
            @ftruncate($filePointer, 0);
            @fclose($filePointer);
        }
    }

}
