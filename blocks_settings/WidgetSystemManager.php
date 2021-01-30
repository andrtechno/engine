<?php

namespace panix\engine\blocks_settings;

use panix\engine\CMS;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\base\Component;

class WidgetSystemManager extends Component
{

    public function getSystemClass($alias)
    {

        if (class_exists($alias)) {
            $reflect = new \ReflectionClass($alias);
        } else {
            return false;
        }
//echo $alias;
        $widget= new $alias;
        $form = $widget::$form;
//CMS::dump($widget::$form);die;
        $namespace = $reflect->getNamespaceName() . '\\form';
        $fpath = dirname($reflect->getFileName()) . DIRECTORY_SEPARATOR . 'form';

        if (file_exists($fpath)) {

            $test = FileHelper::findFiles($fpath, [
                'only' => ['*.php'],
                'recursive' => false
            ]);

          //  foreach ($test as $formPath) {
                //$inc = include_once $formPath;
               // CMS::dump($reflect);die;
                //$test = basename($formPath, '.php');
                //$reflect = new \ReflectionClass($test);

               // $classNamespace = $namespace . '\\' . $test;

                return new $form;
          //  }
        } else {

            if (Yii::$app->request->isAjax)
                die('система не обнаружела настройки виджета');
            return false;
        }

    }


    public function getWidgetTitle($alias)
    {
        $arr = explode('.', $alias);
        $numItems = count($arr);
        $i = 0;
        foreach ($arr as $key => $value) {
            if (++$i === $numItems) {
                //\Yii::import("{$alias}"); //import block class
                $class = new $value;

                return (isset($class->title)) ? $class->title : 'Unknown widget title';
                break;
            }
        }
    }

    public function getClass($classNamespace)
    {
        $reflect = new \ReflectionClass($classNamespace);
        $path = dirname($reflect->getFileName()) . DIRECTORY_SEPARATOR . 'form';
        if (file_exists($path)) {
            return new $classNamespace;
        } else {
            return false;
        }
    }

    // public function getConfigurationFormHtml($alias){
//
    // }


}
