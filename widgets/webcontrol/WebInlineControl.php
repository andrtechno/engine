<?php

namespace panix\engine\widgets\webcontrol;

use panix\engine\controllers\WebController;
use Yii;
use yii\web\View;
use yii\base\BaseObject;

/**
 * Class WebInlineControl
 * @package panix\engine\widgets\webcontrol
 */
class WebInlineControl extends BaseObject
{

    public function init()
    {
        if (Yii::$app->user->can('admin') && !Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $this->checkAdminRequest() && Yii::$app->controller instanceof WebController) {
            //Yii::setAlias('@bower', '@vendor/bower-asset');
            $view = Yii::$app->view;
            WebInlineAsset::register($view);
            $this->editmodeJs();
            Yii::$app->getUrlManager()->addRules([
                'webcontrol/<action:[0-9a-zA-Z_\-]+>' => 'webcontrol/<action>'
            ], true);


            $view->on(View::EVENT_BEGIN_BODY, [$this, 'renderToolbar']);
            Yii::$app->controllerMap['webcontrol'] = 'panix\engine\widgets\webcontrol\WebInlineController';
        }
    }

    public function renderToolbar()
    {
        echo Yii::$app->view->render('@vendor/panix/engine/widgets/webcontrol/views/run');
    }

    private function checkAdminRequest()
    {
        $path = Yii::$app->request->getPathInfo();
        if (empty($path))
            return true;

        if (strpos($path, 'admin') !== false) {
            return false;
        }
        return true;
    }

    private function editmodeJs()
    {
        \panix\ext\tinymce\TinyMceAsset::register(Yii::$app->view);
        Yii::$app->view->registerJs("
function tinymce_ajax(obj){
    var form = obj.formElement;
    var str = $(form).serialize();
    str+='&edit_mode=1&redirect=0';

    $.ajax({
        type:$(form).attr('method'),
        url:$(form).attr('action'),
        data:str,
        dataType:'json',
        beforeSend:function(){
            progressState(obj,true);
        },
        success: function(response){
            if(response.errors !== undefined){
                $.each(response.errors, function (key, data) {
                    common.notify(data,'error');
                });
            }else{
                 common.notify(response.message,'success');
            }
            progressState(obj,false);
        },
        error:function(jqXHR, textStatus, errorThrown){
            console.log(textStatus);
            console.log(jqXHR);
            common.notify('Ошабка: ','error');
            progressState(obj,false);
        }
    });
}

function progressState(obj,bool){
     obj.setProgressState(bool);
}
tinymce.init({
    selector: '.edit_mode_title',
    language : common.language,
    inline: true,
    width : 100,
    plugins: 'save',
    toolbar: 'save undo redo',
    menubar: false,
    toolbar_items_size: 'small',
    save_enablewhendirty: true,
    save_onsavecallback: function() {
        console.log(this);
        tinymce_ajax(this);
    },

});

tinymce.init({
    selector: '.edit_mode_text',
    language : common.language,
    inline: true,
    width : 200,
    plugins: 'save',
    toolbar: 'save undo redo | styleselect | bold italic | alignleft aligncenter alignright',
    menubar: false,
    toolbar_items_size: 'small',
    save_onsavecallback: function() {
        console.log(this);
        tinymce_ajax(this);
    },


});
");
    }

}
