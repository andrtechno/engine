<?php

namespace panix\engine\db;

use panix\engine\components\Settings;
use panix\mod\admin\models\GridColumns;


class Migration extends \yii\db\Migration
{

    public $tableOptions = null;
    public $tableName;
    public $settingsForm = null;

    public function init()
    {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        parent::init();
    }

    /**
     * @param string $gridId
     * @param $model
     * @param array $columns
     */
    public function loadColumns($gridId, $model, $columns = [])
    {
        foreach ($columns as $key => $column) {
            $this->batchInsert(GridColumns::tableName(), ['grid_id', 'modelClass', 'column_key'], [
                [$gridId, $model, $column]
            ]);
        }
    }

    public function loadSettings()
    {
        $settings = [];
        if ($this->settingsForm) {
            $form = $this->settingsForm;
            foreach ($form::defaultSettings() as $key => $value) {
                $settings[] = [$form::$category, $key, $value];
            }
            $this->batchInsert(Settings::tableName(), ['category', 'param', 'value'], $settings);
        }
    }

    public function createTable($table, $columns, $options = null)
    {
        if (!$options) {
            $options = $this->tableOptions;
        }
        parent::createTable($table, $columns, $options);

    }

    /**
     * TODO: need add unique param
     * @param array $indexes
     */
    public function createIndexes($indexes = array())
    {
        foreach ($indexes as $index) {
            $this->createIndex($index, $this->tableName, $index, false);
        }
    }

}
