<?php

namespace panix\engine;

use Yii;
use panix\mod\admin\models\Modules;

class Application extends \yii\web\Application {

    const version = '0.1a';

    public function run() {
        $this->name = $this->settings->get('app', 'sitename');
        $langManager = $this->languageManager;
        $user = $this->user;
        if (!$user->isGuest) {
            $this->language = $langManager->default->code;
        } else {
            $this->language = $langManager->default->code;
        }

        parent::run();
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
        return Yii::t('app', 'COPYRIGHT', ['year' => date('Y')]);
    }

    public function getVersion() {
        return self::version;
    }

    public function init() {
        $modulesList = array_filter(glob(Yii::getAlias('@app/modules/*')), 'is_dir');


        foreach ($this->getModules() as $id => $module) {
            $this->setAliases([
                '@' . $id => realpath(Yii::getAlias("@vendor/panix/mod-{$id}")),
            ]);
            $this->registerTranslations($id);
        }
        
        foreach($modulesList as $module){
            $id = basename($module);
            $this->setAliases([
                '@' . $id => realpath(Yii::getAlias("@app/modules/{$id}")),
            ]);
            $this->registerTranslations($id);
        }        
        
        parent::init();
        $this->setCmsModules();
    }

    private function setCmsModules() {
        $mods = Modules::getEnabled();
        if ($mods) {
            foreach ($mods as $module) {
                $name = $module->name;
                $this->setModule($name, [
                        'class' => str_replace('/', DIRECTORY_SEPARATOR, "app/modules/$name/Module"),
                    ]);
            }
        }
    }

    public function getTranslationsFileMap($id) {
        $lang = $this->language;
        $result = array();
        $basepath = realpath(Yii::getAlias("@{$id}/messages/{$lang}"));
        if (is_dir($basepath)) {
            $fileList = \yii\helpers\FileHelper::findFiles($basepath, [
                        'only' => ['*.php'],
                        'recursive' => false
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
