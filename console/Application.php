<?php

namespace panix\engine\console;

use panix\mod\admin\models\Modules;
use Yii;

class Application extends \yii\console\Application
{


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
        parent::init();
    }

    private function setEngineModules()
    {
        /*$mods = (new Modules)->getEnabled();
        if ($mods) {
            foreach ($mods as $module) {
                $this->setModule($module->name, [
                    'class' => $module->className,
                ]);
            }
        }*/
    }
    /**
     * @param string $id
     * @param string $path
     * @return array
     */
    public function getTranslationsFileMap($id, $path)
    {
        $lang = $this->language;
        $result = [];
        $basePath = realpath(Yii::getAlias("{$path}/{$lang}"));

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
        $path = '@' . $id . '/messages';
        $this->i18n->translations[$id . '/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => $path,
            'fileMap' => $this->getTranslationsFileMap($id, $path)
        ];
    }
}