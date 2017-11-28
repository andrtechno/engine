<?php

namespace panix\engine\actions;

use Yii;
use yii\helpers\Json;

class SwitchAction extends \yii\rest\Action {

    public function run() {
        $json = [];
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {// && isset($_REQUEST)
            $model = new $this->modelClass;
            //$entry = $model->find()->where(['id' => $_REQUEST['id']])->all();
            $entry = $model->find()->where(['id' => $_REQUEST['id']])->all();

            if ($entry) {
                foreach ($entry as $obj) {
                    if (!in_array($obj->primaryKey, $model->disallow_delete)) {
                        if (isset($_REQUEST['s'])) {
                            $obj->switch = $_REQUEST['s'];
                            $obj->update(false);
                            $json = [
                                'status' => 'success',
                                'message' => Yii::t('app', 'SUCCESS_RECORD_SWITCG')
                            ];
                        } else {
                            $json = array(
                                'status' => 'error',
                                'message' => Yii::t('app', 'ERROR_RECORD_SWITCG')
                            );
                        }
                    } else {
                        $json = array(
                            'status' => 'error',
                            'message' => Yii::t('app', 'ERROR_RECORD_SWITCG')
                        );
                    }
                }
            }
        }else{
            
        }
        echo Json::encode($json);
        Yii::$app->end();
    }

}
