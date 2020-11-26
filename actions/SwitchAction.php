<?php

namespace panix\engine\actions;

use Yii;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\rest\Action;

class SwitchAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $json = [];
        $json['success'] = false;
        if (Yii::$app->request->isAjax) {
            /* @var $modelClass ActiveRecordInterface */
            $modelClass = $this->modelClass;

            $entry = $modelClass::findAll($_REQUEST['id']);
            if ($entry) {
                foreach ($entry as $obj) {

                    if (!in_array($obj::primaryKey(), $obj->disallow_delete)) {
                        if (isset($_REQUEST['value'])) {
                            $obj->switch = $_REQUEST['value'];
                            $obj->update(false);
                            $message = ($obj->switch) ? 'SUCCESS_RECORD_ON' : 'SUCCESS_RECORD_OFF';
                            $sw = ($obj->switch) ? 0 : 1;
                            $json['success'] = true;
                            $json['url'] = Yii::$app->request->baseUrl . '/' . Yii::$app->request->pathInfo . "?id={$_REQUEST['id']}&value={$sw}";
                            $json['value'] = $sw;
                            $json['message'] = Yii::t('app/default', $message);
                        } else {
                            $json['message'] = Yii::t('app/default', 'ERROR_RECORD_SWITCH');
                        }
                    } else {
                        $json['message'] = Yii::t('app/default', 'ERROR_RECORD_SWITCH');
                    }
                }
            } else {
                die('error');
            }
        } else {
            die('error2');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }
}
