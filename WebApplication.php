<?php

namespace panix\engine;

use Yii;
use panix\mod\admin\models\Modules;
use yii\web\Application;

/**
 * Class WebApplication
 * @package panix\engine
 */
class WebApplication extends Application
{

    const version = '2.0.0-alpha';

    public function run()
    {
        $this->name = $this->settings->get('app', 'sitename');
        $langManager = $this->languageManager;
        $this->language = (isset($langManager->default->code)) ? $langManager->default->code : $this->language;
        return parent::run();
    }

    public function getModulesInfo()
    {
        $modules = $this->getModules();
        if (YII_DEBUG)
            unset($modules['debug'], $modules['gii'], $modules['admin']);
        $result = [];
        foreach ($modules as $name => $className) {
            //$info = $this->getModule($name)->info;
            if (isset($this->getModule($name)->info))
                $result[$name] = $this->getModule($name)->info;
        }

        return $result;
    }

    public static function powered()
    {
        return Yii::t('app', 'COPYRIGHT', [
            'year' => date('Y'),
            'by' => Html::a('PIXELION CMS', '//pixelion.com.ua')
        ]);
    }

    public function getVersion()
    {
        return self::version;
    }

    public function init()
    {
        $this->setEngineModules();
        foreach ($this->getModules() as $id => $module) {
            if (isset($module['class'])) {
                $reflectionClass = new \ReflectionClass($module['class']);
                $this->setAliases([
                    '@' . $id => realpath(dirname($reflectionClass->getFileName())),
                ]);
                $this->registerTranslations($id);
            }
        }
        $modulesList = array_filter(glob(Yii::getAlias('@app/modules/*')), 'is_dir');
        foreach ($modulesList as $module) {
            $id = basename($module);
            $this->setAliases([
                '@' . $id => realpath(Yii::getAlias("@app/modules/{$id}")),
            ]);
            $this->registerTranslations($id);
        }

        parent::init();
    }

    private function setEngineModules()
    {
        $mods = (new Modules)->getEnabled();
        if ($mods) {
            foreach ($mods as $module) {
                $this->setModule($module->name, [
                    'class' => $module->className,
                ]);
            }
        }
    }

    public function getTranslationsFileMap($id)
    {
        $lang = $this->language;
        $result = [];
        $basePath = realpath(Yii::getAlias("@{$id}/messages/{$lang}"));
        if (is_dir($basePath)) {
            $fileList = \yii\helpers\FileHelper::findFiles($basePath, [
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

    public function registerTranslations($id)
    {
        $this->i18n->translations[$id . '*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@' . $id . '/messages',
            'fileMap' => $this->getTranslationsFileMap($id)
        ];
    }

}
