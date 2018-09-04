<?php

namespace panix\engine\actions;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use yii\rest\Action;

class SwitchAction extends Action {

    public function run() {
        $json = [];
        if (Yii::$app->request->isAjax) {// && isset($_REQUEST)
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
                                'message' => Yii::t('app', 'SUCCESS_RECORD_SWITCH')
                            ];
                        } else {
                            $json = array(
                                'status' => 'error',
                                'message' => Yii::t('app', 'ERROR_RECORD_SWITCH')
                            );
                        }
                    } else {
                        $json = array(
                            'status' => 'error',
                            'message' => Yii::t('app', 'ERROR_RECORD_SWITCH')
                        );
                    }
                }
            }else{
                die('error');
            }
        }else{
            die('error2');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ArrayHelper::toArray($json);
        //return Json::encode($json);
        Yii::$app->end();
    }

}
