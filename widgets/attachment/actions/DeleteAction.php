<?php

/**
 * Это действие вызывается при adminList виджета для удаление записей или записи.
 * 
 * Пример кода для контроллера:
 * <code>
 * public function actions() {
 *      return array(
 *          'delete' => array(
 *              'class' => 'ext.adminList.actions.DeleteAction',
 *          )
 *      );
 * }
 * </code>
 *
 * @author PIXELION CMS development team <info@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 * @package ext
 * @subpackage adminList.actions
 * @uses CAction
 */
class DeleteAction extends CAction {

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
                        if (!in_array($page->primaryKey, $model->disallow_delete)) {
                            $alias = $_REQUEST['alias'];

                            if (file_exists(Yii::getPathOfAlias("webroot.uploads.attachments.{$alias}") . '/' . $page->name))
                                unlink(Yii::getPathOfAlias("webroot.uploads.attachments.{$alias}") . '/' . $page->name);

                            if (file_exists(Yii::getPathOfAlias("webroot.assets.attachments.{$alias}"))) {
                                $listfile = CFileHelper::findFiles(Yii::getPathOfAlias("webroot.assets.attachments.{$alias}"), array(
                                            'absolutePaths' => true
                                ));
                                foreach ($listfile as $path) {
                                    if (strpos($path, $page->name) !== false) {
                                        if (file_exists($path)) {
                                            unlink($path);
                                        }
                                    }
                                }
                            }

                            $page->delete(); //$page->deleteByPk($_REQUEST['id']);

                            if ($page->is_main) {
                                // Get first image and set it as main
                                $model = AttachmentModel::model()->find();
                                if ($model) {
                                    $model->is_main = 1;
                                    $model->save(false, false, false);
                                }
                            }

                            $json = array(
                                'status' => 'success',
                                'message' => Yii::t('app', 'SUCCESS_RECORD_DELETE')
                            );
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
