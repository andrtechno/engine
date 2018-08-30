<?php

namespace panix\engine\blocks_settings;

use Yii;
use yii\helpers\FileHelper;

class WidgetSystemManager extends \yii\base\Component {

    public function getSystemClass($alias) {

        $reflect = new \ReflectionClass($alias);


        $namespace = $reflect->getNamespaceName().'\\form';
        $fpath = dirname($reflect->getFileName()) . DIRECTORY_SEPARATOR . 'form';

        if (file_exists($fpath)) {
    
            $test = FileHelper::findFiles($fpath, [
                        'only' => ['*.php'],
                        'recursive' => false
            ]);

            foreach ($test as $formpath) {
                $test = basename($formpath,'.php');
                $classNamespace = $namespace.'\\'.$test;

                return new $classNamespace;
            }
        } else {

            if (Yii::$app->request->isAjax)
                die('система не обнаружела настройки виджета');
            return false;
        }

    }


    public function getWidgetTitle($alias) {
        $arr = explode('.', $alias);
        $numItems = count($arr);
        $i = 0;
        foreach ($arr as $key => $value) {
            if (++$i === $numItems) {
                Yii::import("{$alias}"); //import block class
                $class = new $value;

                return (isset($class->title)) ? $class->title : 'Unknown widget title';
                break;
            }
        }
    }
    public function getClass($classNamespace) {
        $reflect = new \ReflectionClass($classNamespace);
        $path = dirname($reflect->getFileName()) . DIRECTORY_SEPARATOR . 'form';
        if (file_exists($path)) {
            return new $classNamespace;
        } else {

            return false;
        }
    }



}
