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
        if (Yii::$app->request->isAjax) {
            /* @var $modelClass ActiveRecordInterface */
            $modelClass = $this->modelClass;

            $entry = $modelClass::findAll($_REQUEST['id']);
            if ($entry) {
                foreach ($entry as $obj) {

                    if (!in_array($obj::primaryKey(), $obj->disallow_delete)) {
                        if (isset($_REQUEST['s'])) {
                            $obj->switch = $_REQUEST['s'];
                            $obj->update(false);
                            $message = ($obj->switch) ? 'SUCCESS_RECORD_ON' : 'SUCCESS_RECORD_OFF';
                            $sw = ($obj->switch) ? 0 : 1;
                            $json = [
                                'value' => $sw,
                                'status' => 'success',
                                'url' => '/' . Yii::$app->request->pathInfo . "?id={$_REQUEST['id']}&s={$sw}",
                                'message' => Yii::t('app', $message)
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
            } else {
                die('error');
            }
        } else {
            die('error2');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ArrayHelper::toArray($json);
    }
}
