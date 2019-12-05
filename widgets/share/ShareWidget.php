<?php

namespace panix\engine\widgets\share;


use panix\engine\data\Widget;

class ShareWidget extends Widget
{
    public $name;
    public $url;
    public $facebook = true;
    public $twitter = true;
    public $pinterest = true;
    public $linkedin = true;

    public function run()
    {
        $this->view->registerJs("
        $(document).on('click','.priduct_social a',function(){
            window.open($(this).attr('href'), 'Поделиться', 'width=500,height=500'); return false;
        });");
        return $this->render($this->skin, ['name' => $this->name, 'url' => $this->url]);
    }
}