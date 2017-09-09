<?php

namespace panix\engine;

class Application extends \yii\web\Application {

    const version = '0.1a';

    public function run() {
        $this->name = $this->settings->get('app', 'sitename');


        parent::run();
    }

    public function _meta_page() {
        
    }

    public function getModulesInfo() {
        $modules = $this->getModules();
        if (YII_DEBUG)
            unset($modules['debug'], $modules['gii'], $modules['admin']);
        $result = array();
        foreach ($modules as $name => $className) {
            //$info = $this->getModule($name)->info;
            if (isset($this->getModule($name)->info))
                $result[$name] = $this->getModule($name)->info;
        }

        return $result;
    }

    public static function powered() {
        return \Yii::t('app', 'COPYRIGHT', [
                    'year' => date('Y')
        ]);
    }

    public function getVersion() {
        return self::version;
    }

    public function init() {
        //     $this->setEngineModules();
        foreach (\Yii::$app->getModules() as $id => $module) {
            $this->setAliases([
                '@' . $id => realpath(\Yii::getAlias("@vendor/panix/mod-{$id}")),
            ]);
            $this->registerTranslations($id);
        }

        parent::init();
    }

    public function getTranslationsFileMap($id) {
        $lang = $this->language;
        $result = array();
        $basepath = realpath(\Yii::getAlias("@{$id}/messages/{$lang}"));
        if (is_dir($basepath)) {
            $fileList = \yii\helpers\FileHelper::findFiles($basepath, [
                        'only' => ['*.php'],
                        'recursive' => FALSE
            ]);

            foreach ($fileList as $path) {
                $result[$id . '/' . basename($path, '.php')] = basename($path);
            }
        } else {
            $result = [];
        }

        return $result;
    }

    public function registerTranslations($id) {
        $this->i18n->translations[$id . '/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@' . $id . '/messages',
            'fileMap' => $this->getTranslationsFileMap($id)
        ];
    }

}
