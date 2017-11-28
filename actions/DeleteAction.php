<?php

namespace panix\engine\actions;

use Yii;
use yii\helpers\Json;

class DeleteAction extends \yii\rest\Action {

    public function run() {
        $json = [];
        if (Yii::$app->request->isPost && isset($_REQUEST)) {
            $model = new $this->modelClass;
            $entry = $model->find()->where(['id' => $_REQUEST['id']])->all();
            if ($entry) {
                foreach ($entry as $obj) {
                    if (!in_array($obj->primaryKey, $model->disallow_delete)) {
                        $obj->delete();
                        $json = [
                            'status' => 'success',
                            'message' => Yii::t('app', 'SUCCESS_RECORD_DELETE')
                        ];
                    } else {
                        $json = array(
                            'status' => 'error',
                            'message' => Yii::t('app', 'ERROR_RECORD_DELETE')
                        );
                    }
                }
            }
        }
        echo Json::encode($json);
        Yii::$app->end();
    }

}
