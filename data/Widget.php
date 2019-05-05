<?php

namespace panix\engine\data;

use Yii;
use yii\helpers\VarDumper;

class Widget extends \yii\base\Widget
{

    public $skin = 'default';
    public $assetsUrl;
    public $widget_id;

    public function init()
    {
        parent::init();
        $reflectionClass = new \ReflectionClass(get_class($this));
        $this->widget_id = 'wgt_' . $reflectionClass->getShortName();
        Yii::$app->setAliases([
            '@' . $this->widget_id => realpath(dirname($reflectionClass->getFileName())),
        ]);


        $this->registerTranslations($this->widget_id);

        if (file_exists(Yii::getAlias("@{$this->widget_id}/assets"))) {
            $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@{$this->widget_id}/assets"));
            $this->assetsUrl = $assetsPaths[1];
        }

        // echo VarDumper::dump(Yii::$app->i18n->translations,15,true);die;

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

    public function getTitle()
    {
        $name = $this->getName();
        if (file_exists(Yii::getAlias("@{$this->widget_id}/messages"))) {
            return Yii::t("wgt_{$name}/default", 'TITLE');
        } else {
            return $name;
        }


    }

    public function getConfig()
    {
        return Yii::$app->settings->get($this->getName());
    }

    public function getName()
    {
        return basename(get_class($this));
    }

}
