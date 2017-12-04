<?php

namespace panix\engine\db;

class Migration extends \yii\db\Migration {

    public $tableOptions = null;
    public $tableName;

    public function init() {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        parent::init();
    }

    public function createTable($table, $columns, $options = null) {
        if (!$options) {
            $options = $this->tableOptions;
        }
        parent::createTable($table, $columns, $options);
    }

    /**
     * TODO: need add unique param
     * @param array $indexes
     */
    public function createIndexes($indexes = array()) {
        foreach ($indexes as $index) {
            $this->createIndex($index, $this->tableName, $index, false);
        }
    }

}
