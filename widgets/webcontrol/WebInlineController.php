<?php

namespace panix\engine\widgets\webcontrol;

use panix\engine\controllers\WebController;
use panix\mod\admin\models\Notifications;

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

        return \yii\helpers\Json::encode($result);
        die;
    }

    public function actionAjaxReadNotifaction($id)
    {

        //$notifactions = Notifactions::find()->where(['id'=>$id])->one();
        $notifactions = Notifications::findOne($id);
        $notifactions->is_read = 1;
        $notifactions->save(false);

        return \yii\helpers\Json::encode(['ok']);
        die;
    }

}
