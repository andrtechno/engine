<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace panix\engine\widgets\webcontrol;

use panix\mod\admin\models\Notifactions;

/**
 * Webcontrol of Controller
 *
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @author Semenov Andrew <andrew.panix@gmail.com>
 * @link http://corner-cms.com Website CORNER CMS
 * 
 */
class WebInlineController extends \panix\engine\controllers\WebController {

    public function actionIndex() {
        print_r($this->id);
        echo 'sdadsa';
    }

    public function actionAjaxCounters() {

        $notifactions = Notifactions::find()->read(0)->all();
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

    public function actionAjaxReadNotifaction($id) {

        //$notifactions = Notifactions::find()->where(['id'=>$id])->one();
        $notifactions = Notifactions::findOne($id);
        $notifactions->is_read = 1;
        $notifactions->save(false);

        return \yii\helpers\Json::encode(['ok']);
        die;
    }

}
