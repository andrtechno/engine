<?php

namespace panix\engine\widgets\webcontrol;

use Yii;
use panix\engine\controllers\WebController;
use panix\mod\admin\models\Notifications;
use yii\web\Response;

/**
 * Webcontrol of Controller
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 *
 */
class WebInlineController extends WebController
{

    public function actionIndex()
    {
        print_r($this->id);
        echo 'sdadsa';
    }

    public function actionAjaxCounters()
    {


        $notifactions = Notifications::find()->read(0)->all();
        $result = [];
        $result['count']['cart'] = 5;
        $result['count']['comments'] = 10;
        $result['notify'] = [];
        foreach ($notifactions as $notify) {
            $result['notify'][$notify->id] = [
                'text' => $notify->text,
                'type' => $notify->type
            ];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    public function actionAjaxReadNotifaction($id)
    {

        //$notifactions = Notifactions::find()->where(['id'=>$id])->one();
        $notifactions = Notifications::findOne($id);
        $notifactions->is_read = 1;
        $notifactions->save(false);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['ok'];
    }

}
