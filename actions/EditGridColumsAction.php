<?php

/**
 * EditGridColumsAction class.
 * 
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @license http://corner-cms.com/license.txt CORNER CMS License
 * @link http://corner-cms.com CORNER CMS
 * @package ext
 * @subpackage adminList.actions
 * @uses CAction
 */
class EditGridColumsAction extends CAction {

    public function run() {
        if (Yii::app()->request->isAjaxRequest) {
            //Yii::app()->clientScript->registerCoreScript('jquery.ui');
            Yii::app()->clientScript->scriptMap = array(
                'jquery.js' => false,
                'jquery.ba-bbq.js' => false,
            );
            $modelClass = $_POST['model'];
            $grid_id = $_POST['grid_id'];
            $mod = $_POST['module'];
            $upMod = ucfirst($mod);
            Yii::import("mod.{$mod}.models.{$modelClass}");
            Yii::import("mod.{$mod}.{$upMod}Module");
            if (isset($_POST['GridColumns'])) {
                GridColumns::model()->deleteAllByAttributes(array('grid_id' => $grid_id));
                foreach ($_POST['GridColumns']['check'] as $key => $post) {
                    $model = new GridColumns;
                    $model->grid_id = $grid_id;
                    $model->module = $mod;
                    // $model->name = $post;
                    $model->ordern = $_POST['GridColumns']['ordern'][$key];
                    $model->key = $key;
                    try {
                        $model->save(false, false, false);
                    } catch (CDbException $e) {
                        //error
                    }
                }
            }

            $data = array();
            $cr = new CDbCriteria;
            $cr->order = '`t`.`ordern` DESC';
            $cr->condition = '`t`.`grid_id`=:grid';
            $cr->params = array(
                ':grid' => $grid_id,
            );
            $model = GridColumns::model()->findAll($cr);
            $m = array();
            foreach ($model as $r) {
                $m[$r->key]['ordern'] = $r->ordern;
                $m[$r->key]['key'] = $r->key;
            }

            $columsArray = $modelClass::model()->getGridColumns();

            unset($columsArray['DEFAULT_COLUMNS'], $columsArray['DEFAULT_CONTROL']);
            if(isset($columsArray)){
            foreach ($columsArray as $key => $column) {
                if (isset($column['header'])) {
                    $name = $column['header'];
                } else {
                    $name = $modelClass::model()->getAttributeLabel($column['name']);
                }
                if (isset($m[$key])) {
                    $isChecked = ($m[$key]['key'] == $key) ? true : false;
                } else {
                    $m[$key] = 0;
                    $isChecked = false;
                }

                $data[] = array(
                    'checkbox' => Html::checkbox('GridColumns[check][' . $key . ']', $isChecked, array('value' => $name)),
                    'name' => $name,
                    'sort' => Html::textField('GridColumns[ordern][' . $key . ']', $m[$key]['ordern'], array('class' => 'form-control text-center'))
                );
            }
            }
            $provider = new CArrayDataProvider($data, array('keyField'=>false,'pagination' => false));
            $this->controller->render('ext.adminList.views._EditGridColumns', array('modelClass' => $modelClass, 'provider' => $provider, 'grid_id' => $grid_id, 'module' => $mod));
        } else {
            throw new CHttpException(401);
        }
    }

}
