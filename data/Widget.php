<?php

namespace panix\engine\data;

use Yii;

class Widget extends \yii\base\Widget {

    public $skin = 'default';
    public $assetsUrl;

    public function init() {
        parent::init();
        $reflectionClass = new \ReflectionClass(get_class($this));
        $aliasName = 'wgt_' . $reflectionClass->getShortName();
        Yii::$app->setAliases([
            '@' . $aliasName => realpath(dirname($reflectionClass->getFileName())),
        ]);
        $this->registerTranslations($aliasName);

        if (file_exists(Yii::getAlias("@{$aliasName}/assets"))) {
            $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@{$aliasName}/assets"));
            $this->assetsUrl = $assetsPaths[1];
        }


    }

    public function getTranslationsFileMap($id) {
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

    public function registerTranslations($id) {
        Yii::$app->i18n->translations[$id . '/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@' . $id . '/messages',
            'fileMap' => $this->getTranslationsFileMap($id)
        ];
    }

    public function getTitle() {
        return basename(get_class($this));
    }

    public function getConfig() {
        return Yii::$app->settings->get($this->getName());
    }

    public function getName() {
        return basename(get_class($this));
    }

}
