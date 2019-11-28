<?php

namespace panix\engine\data;

use Yii;
use ReflectionClass;

class Widget extends \yii\base\Widget
{

    public $skin = 'default';
    public $assetsUrl;
    public $widget_id;
    public $viewPath;
    protected $reflectionClass;

    public function init()
    {
        $this->reflectionClass = new ReflectionClass($this);
        parent::init();

        $this->widget_id = 'wgt_' . $this->getName();

        Yii::$app->setAliases([
            '@' . $this->widget_id => realpath(dirname($this->reflectionClass->getFileName())),
        ]);

       // $this->registerTranslations($this->widget_id);

        if (file_exists(Yii::getAlias("@{$this->widget_id}/assets"))) {
            $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@{$this->widget_id}/assets"));
            $this->assetsUrl = $assetsPaths[1];
        }
        $this->registerTranslations($this->widget_id);
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
        if (file_exists(Yii::getAlias("@{$this->widget_id}/messages"))) {
            return Yii::t("{$this->widget_id}/default", 'TITLE');
        } else {
            return $this->name;
        }
    }

    public function getConfig()
    {
        return Yii::$app->settings->get($this->widget_id);
    }

    public function getName()
    {
        return $this->reflectionClass->getShortName();
    }


    public function getViewPath()
    {
        $class = new ReflectionClass($this);
        $diename = dirname($class->getFileName());

        if ($this->viewPath) {
            return Yii::getAlias($this->viewPath);
        }

        $views = [
            "@app/widgets/{$diename}",
        ];

        foreach ($views as $view) {
            if (file_exists(Yii::getAlias($view))) {
                Yii::debug('Layout load ' . $view, __METHOD__);
                return $view;
            }
        }
        return parent::getViewPath();
    }

}
