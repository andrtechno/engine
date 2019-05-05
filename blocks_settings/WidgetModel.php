<?php

namespace panix\engine\blocks_settings;

use Yii;
use yii\base\Model;

class WidgetModel extends Model
{

    protected $widget_id;
    public function attributeLabels()
    {
        $class = (new \ReflectionClass(get_called_class()));
        $labels = [];
        foreach ($this->attributes as $attr => $val) {
            $labels[$attr] = Yii::t($this->widget_id . '/' . $class->getShortName(), strtoupper($attr));
        }
        return $labels;
    }


    public function init()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));
        $this->widget_id = 'wgt_' . $reflectionClass->getShortName();

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

        return Yii::$app->settings->get('wgt_'.basename($id));
    }

    public function getConfigurationFormHtml($obj)
    {


        $this->attributes = (array) $this->getSettings($obj);


        $ref = new \ReflectionClass($this);
        Yii::setAlias('@viewPath', dirname(dirname($ref->getFileName())) . DIRECTORY_SEPARATOR . 'views');
        return Yii::$app->controller->renderPartial('@viewPath/_form', ['model' => $this]);
    }

    public function saveSettings($obj, $postData)
    {
        $reflect = new \ReflectionClass($this);
        $this->setSettings(basename($obj), $postData[$reflect->getShortName()]);
    }

    public function setSettings($obj, $data)
    {
        if ($data) {
            Yii::$app->settings->set('wgt_'.$obj, $data);
        }
    }


}
