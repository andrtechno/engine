<?php

namespace panix\engine\actions;

/**
 * Это действие вызывается при adminList виджета для скрыть/показать записи или запись.
 * 
 * Пример кода для контроллера:
 * <code>
 * public function actions() {
 *      return array(
 *          'switch' => array(
 *              'class' => 'ext.adminList.actions.SwitchAction',
 *          )
 *      );
 * }
 * </code>
 * 
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @license http://corner-cms.com/license.txt CORNER CMS License
 * @link http://corner-cms.com CORNER CMS
 * @package ext
 * @subpackage adminList.actions
 * @uses CAction
 * 
 * @property integer $_REQUEST['id'] Массив записей
 * @property string $_REQUEST['model'] Модель 
 * @property integer $_REQUEST['switch'] 1|0 
 */
class SwitchAction extends \yii\base\Action {

    public $modelName;

    /**
     * Запустить действие
     */
    public function run() {
        // if (isset($_REQUEST)) {
        //if (Yii::$app->request->isPost) {
        /* $model = call_user_func(array($_REQUEST['model'], 'model'));
          $entry = $model->findAllByPk($_REQUEST['id']);
          if (!empty($entry)) {
          foreach ($entry as $page) {
          $page->updateByPk($_REQUEST['id'], array('switch' => $_REQUEST['switch']));
          }
          }

          } */
        // }
    }

}
