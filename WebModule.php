<?php

namespace panix\engine;

use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;
use panix\mod\rbac\filters\AccessControl;

/**
 * Class WebModule
 * @package panix\engine
 */
class WebModule extends Module
{

    public $assetsUrl;
    // protected $_icon;
    public $count = false;
    // protected $info;
    public $routes = [];
    //  public static $moduleID;
    public $modelClasses = [];
    protected $_models;
    public $icon;
    public $uploadPath;
    public $uploadAliasPath = null;


    public function behaviors2()
    {
        return [
            AccessControl::class
        ];
    }

    public function getMdFiles()
    {
        $list = [];
        $files = FileHelper::findFiles(Yii::getAlias('@' . $this->id), [
            'only' => ['*.md'],
            'recursive' => false,
            'caseSensitive' => false
        ]);
        foreach ($files as $file) {
            $list[basename($file, '.md')] = $file;
        }
        return $list;
    }


    public function init()
    {
        // echo Yii::getAlias('@web');die;
        if (!in_array(Yii::$app->id, ['console', 'api'])) {
            if (file_exists(Yii::getAlias("@{$this->id}/assets"))) {
                $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@{$this->id}/assets"));
                $this->assetsUrl = $assetsPaths[1];
            }
        }

        if (Yii::$app->id == 'backend') {
            $baseNamespace = dirname(get_class($this));
            // $this->controllerNamespace = $baseNamespace . "\\controllers\\admin";
            //  $this->setViewPath($this->getBasePath() . DIRECTORY_SEPARATOR . 'views'.DIRECTORY_SEPARATOR.'admin');

            //  $this->controllerPath = "@{$this->id}/admin";

        }
        if (Yii::$app->id == 'console') {
            $baseNamespace = dirname(get_class($this));
            $reflector = new \ReflectionClass(get_class($this));
            if (file_exists(dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . 'commands')) {
                $this->controllerNamespace = $baseNamespace . "\\commands";
            }
        }
        //$this->registerTranslations();

        $this->uploadAliasPath = "@app/web/uploads/content/{$this->id}";
        $this->uploadPath = "/uploads/content/{$this->id}";

        if (method_exists($this, 'getDefaultModelClasses')) {
            $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        }


        //Yii::configure($this, ['config'=>(array) Yii::$app->settings->get($this->id)]);

        parent::init();
    }

    public function afterInstall()
    {

        // $reflectionClass = new \ReflectionClass(static::class);

        // $test = $this::getInstance();
//print_r(Yii::getAlias('@'.($this->id)));


        // $fileName2 = (new \ReflectionClass(new \panix\engine\WebModule($this->id)))->getFileName();

        // $fileName = (new \ReflectionClass(get_called_class()))->getFileName();
        // print_r($fileName2);


        /// print_r($reflectionClass->getNamespaceName());
        //die;

        // if ($this->uploadAliasPath && !file_exists(Yii::getPathOfAlias($this->uploadAliasPath)))
        //     CFileHelper::createDirectory(Yii::getPathOfAlias($this->uploadAliasPath), 0777);
        //Yii::$app->cache->flush();
        // Yii::app()->widgets->clear();
        return true;
    }

    /**
     * Method will be called after module removed
     */
    public function afterUninstall()
    {
        //if ($this->uploadAliasPath && !file_exists(Yii::getPathOfAlias($this->uploadAliasPath)))
        //    CFileHelper::removeDirectory(Yii::getPathOfAlias($this->uploadAliasPath), array('traverseSymlinks' => true));
        //if (file_exists(Yii::getPathOfAlias("webroot.uploads.attachments.{$this->id}")))
        //    CFileHelper::removeDirectory(Yii::getPathOfAlias("webroot.uploads.attachments.{$this->id}"), array('traverseSymlinks' => true));
        // Yii::$app->cache->flush();
        //$moduleName = ucfirst($this->id) . '.';


        return true;
    }

    // public function getIcon() {
    //    return $this->_icon;
    // }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getAuthor()
    {
        return 'dev@pixelion.com.ua';
    }

    public function getName()
    {
        return Yii::t($this->id . "/default", 'MODULE_NAME');
    }

    public function getDescription()
    {
        return Yii::t($this->id . "/default", 'MODULE_DESC');
    }

    public function getWidgets()
    {
        if (file_exists(Yii::getAlias("@{$this->id}/widgets"))) {

        }
    }

    public function getAdminMenu()
    {
        return [];
    }
}
