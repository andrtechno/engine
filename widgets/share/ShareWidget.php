<?php

namespace panix\engine\widgets\share;


use panix\engine\data\Widget;

class ShareWidget extends Widget
{
    public $name;
    public $url;
    public $image;
    public $facebook = true;
    public $twitter = true;
    public $pinterest = true;
    public $linkedin = true;

    public function run()
    {
        $this->view->registerJs("
        $(document).on('click','a.share',function(){
            window.open($(this).attr('href'), 'Поделиться', 'width=500,height=500'); return false;
        });");
        return $this->render($this->skin, [
            'name' => $this->name,
            'url' => $this->url,
            'image' => $this->image
        ]);
    }

    public function url($url,$params=[]){
     //   $url = 'https://www.facebook.com/sharer/sharer.php';

        $url_parts = parse_url($url);
// If URL doesn't have a query string.
        if (isset($url_parts['query'])) { // Avoid 'Undefined index: query'
            parse_str($url_parts['query'], $params);
        } else {
          //  $params = [];
        }

     //   $params['category'] = 2;     // Overwrite if exists
        $url_parts['query'] = http_build_query($params);
        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];

    }
}