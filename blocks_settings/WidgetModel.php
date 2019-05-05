<?php

namespace panix\engine\blocks_settings;

use Yii;
use yii\base\Model;

class WidgetModel extends Model
{

    private $widget_id;
    public function attributeLabels()
    {
        $class = (new \ReflectionClass(get_called_class()));
        $labels = [];
        $aliasName = 'wgt_' . $class->getShortName();
        foreach ($this->attributes as $attr => $val) {
            $labels[$attr] = Yii::t($this->widget_id . '/' . $class->getShortName(), strtoupper($attr));
        }
        return $labels;
    }


    public function init()
    {

        $reflectionClass = new \ReflectionClass(get_class($this));
        $this->widget_id = 'wgt_' . $reflectionClass->getShortName();
        //die(realpath(dirname(dirname($reflectionClass->getFileName()))));
        Yii::$app->setAliases([
            '@' . $this->widget_id => realpath(dirname(dirname($reflectionClass->getFileName()))),
        ]);


        $this->registerTranslations($this->widget_id);


        parent::init();
    }


    public function getTranslationsFileMap($id)
    {
        $lang = Yii::$app->language;
        $result = [];
        $basepath = realpath(Yii::getAlias("@{$id}/messages/{$lang}"));
        if (file_exists($basepath)) {
            if (is_dir($basepath)) {
                $fileList = \yii\helpers\FileHelper::findFiles($basepath, [
                    'only' => ['*.php'],
                    'recursive' => false
                ]);

                foreach ($fileList as $path) {
                    $result[$id . '/' . basename($path, '.php')] = basename($path);
                }
            }
        }

        return $result;
    }

    public function registerTranslations($id)
    {
        Yii::$app->i18n->translations[$id . '/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@' . $id . '/messages',
            'fileMap' => $this->getTranslationsFileMap($id)
        ];
    }


    public function getSettings($id)
    {
        return Yii::$app->settings->get($id);
    }

    public function getConfigurationFormHtml($obj)
    {

        $className = basename(Yii::getAlias($obj));
        $this->attributes = (array) $this->getSettings($this->widget_id);
        //if (method_exists($this, 'registerScript')) {
        //    $this->registerScript();
        //}

        $ref = new \ReflectionClass($this);
        Yii::setAlias('@viewPath', dirname(dirname($ref->getFileName())) . DIRECTORY_SEPARATOR . 'views');

        return Yii::$app->controller->renderPartial('@viewPath/_form', ['model' => $this]);

        // return $form;
    }

    public function saveSettings($obj, $postData)
    {

        $reflect = new \ReflectionClass($this);



        $this->setSettings($obj, $postData[$reflect->getShortName()]);
    }

    public function setSettings($obj, $data)
    {
        if ($data) {
            $className = basename(Yii::getAlias($obj));
           // $cache = Yii::$app->cache->get(md5(Yii::$app->cache->keyPrefix . $className));
           // if (isset($cache)) {
           //     Yii::$app->cache->delete($className);
           // }



            Yii::$app->settings->set($this->widget_id, $data);
        }
    }


}
