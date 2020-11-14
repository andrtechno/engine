<?php

namespace panix\engine\data;

use Yii;
use ReflectionClass;

class Widget extends \yii\base\Widget
{

    public $skin = 'default';
    public $assetsUrl;
   // public $widget_id;
    public $viewPath;
    protected $reflectionClass;

    static $widget_name;
    static $widget_description;
    static $widget_id;
    public function init()
    {
        $this->reflectionClass = new ReflectionClass($this);
        parent::init();

        static::$widget_id = 'wgt_' . $this->getName();
        $wid = static::$widget_id;
        Yii::$app->setAliases([
            '@' . $wid => realpath(dirname($this->reflectionClass->getFileName())),
        ]);

       // $this->registerTranslations($this->widget_id);

        if (file_exists(Yii::getAlias("@{$wid}/assets"))) {
            $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@{$wid}/assets"));
            $this->assetsUrl = $assetsPaths[1];
        }
        $this->registerTranslations($wid);
    }

    public function getTranslationsFileMap($id)
    {
        $lang = Yii::$app->language;
        $result = [];
        if (file_exists(Yii::getAlias("@{$id}/messages/{$lang}"))) {
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
        }
        return $result;
    }
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t(static::$widget_id.'/' . $category, $message, $params, $language);
    }
    public function registerTranslations($id)
    {
        Yii::$app->i18n->translations[$id . '/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@' . $id . '/messages',
            'fileMap' => $this->getTranslationsFileMap($id)
        ];
    }

    public function getTitle()
    {
        $wid = static::$widget_id;
        if (file_exists(Yii::getAlias("@{$wid}/messages"))) {
            return Yii::t("{$wid}/default", 'TITLE');
        } else {
            return $this->name;
        }
    }

    public function getConfig()
    {
        return Yii::$app->settings->get(static::$widget_id);
    }

    public function getName()
    {
        return $this->reflectionClass->getShortName();
    }


    public function getViewPath()
    {
        $class = new ReflectionClass($this);
        $baseName = basename(dirname($class->getFileName()));

        $views = [
            "@theme/widgets/{$baseName}",
            "@app/widgets/{$baseName}",
        ];

        foreach ($views as $view) {
            if (file_exists(Yii::getAlias($view))) {
                Yii::debug('widget skin load ' . $view, __METHOD__);
                return Yii::getAlias($view);
            }
        }
        return parent::getViewPath();
    }

}
