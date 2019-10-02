<?php

namespace panix\engine\console\controllers;

use Yii;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\helpers\ArrayHelper;


class MigrateController extends BaseMigrateController
{
    public $templateFile = '@vendor/panix/engine/views/migration.php';

    public $generatorTemplateFiles = [
        'create_table' => '@vendor/panix/engine/views/createTableMigration.php',
        'drop_table' => '@vendor/panix/engine/views/dropTableMigration.php',
        'add_column' => '@vendor/panix/engine/views/addColumnMigration.php',
        'drop_column' => '@vendor/panix/engine/views/dropColumnMigration.php',
        'create_junction' => '@vendor/panix/engine/views/createTableMigration.php'
    ];


    public function beforeAction($action)
    {
        $list = [];
        foreach (Yii::$app->getModules() as $mod => $params) {
            $module = Yii::$app->getModule($mod);
            $class = new \ReflectionClass($module);
            //if (in_array($mod, ['contacts'])) {
                $list[] = $class->getNamespaceName() . '\\migrations';
            //}
        }
        $this->migrationNamespaces = ArrayHelper::merge($this->migrationNamespaces,$list);
        return parent::beforeAction($action);

    }
}
