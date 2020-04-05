<?php

namespace panix\engine\log;

use Yii;
use yii\log\FileTarget as BaseFileTarget;
use ZipArchive;

/**
 * Class FileTarget
 * @package panix\engine\log
 */
class FileTarget extends BaseFileTarget
{
    public $logVars = [];

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