<?php

namespace panix\engine\data;

use Yii;

class ActiveDataProvider extends \yii\data\ActiveDataProvider {

    private $_pagination;

    public function setPagination($value) {
        if (is_array($value)) {
            $config = ['class' => Pagination::className()];
            if ($this->id !== null) {
                $config['pageParam'] = $this->id . '-page';
                $config['pageSizeParam'] = $this->id . '-per-page';
            }
            $this->_pagination = Yii::createObject(array_merge($config, $value));

            $modelClass = $this->query->modelClass;
            $mid = $modelClass::MODULE_ID;
            $settings = Yii::$app->settings;
            $this->_pagination->pageSize = ($settings->get($mid, 'pagenum')) ? $settings->get($mid, 'pagenum') : $settings->get('app', 'pagenum');
        } elseif ($value instanceof Pagination || $value === false) {
            $this->_pagination = $value;
        } else {
            throw new InvalidParamException('Only Pagination instance, configuration array or false is allowed.');
        }
    }

    public function getPagination() {
        if ($this->_pagination === null) {
            $this->setPagination([]);
        }

        return $this->_pagination;
    }

}

?>
