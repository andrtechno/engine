<?php

/**
 * Это действие вызывается при adminList виджета для удаление записей или записи.
 * 
 * Пример кода для контроллера:
 * <code>
 * public function actions() {
 *      return array(
 *          'delete' => array(
 *              'class' => 'ext.adminList.actions.DeleteFlagAction',
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
 */
class DeleteFlagAction extends CAction {

    /**
     * @var string 
     */
    public $model;

    /**
     * Запустить действие
     */
    public function run() {
        $json = array();
        if (isset($_REQUEST)) {
            if (Yii::app()->request->isPostRequest) {
                $model = (isset($this->model)) ? call_user_func(array($this->model, 'model')) : call_user_func(array($_REQUEST['model'], 'model'));
                $entry = $model->findAllByPk($_REQUEST['id']);
                if (!empty($entry)) {
                    foreach ($entry as $page) {
                        if (!in_array($page->primaryKey, $model->hidden_delete)) {
                            $page->is_deleted = 1;
                            $page->update();
                            $json = array('status' => 'success', 'message' => Yii::t('app', 'SUCCESS_RECORD_DELETE'));
                        } else {
                            $json = array(
                                'status' => 'error',
                                'message' => Yii::t('app', 'ERROR_RECORD_DELETE')
                            );
                        }
                    }
                }
            }
        }
        echo CJSON::encode($json);
        Yii::app()->end();
    }

}
