<?php

namespace panix\engine;

use Yii;

/**
 * Language patterns of LanguageUrlRule
 *
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @author Semenov Andrew <andrew.panix@gmail.com>
 * @link http://corner-cms.com Website CORNER CMS
 * 
 */
class LanguageUrlRule extends \yii\web\UrlRule {

    public function init() {
        if ($this->pattern !== null) {
            $this->pattern = '<language>/' . $this->pattern;
            // for subdomain it should be:
             //$this->pattern =  'http://<language>.example.com/' . $this->pattern,
        }

        $this->defaults['language'] = Yii::$app->language;
        parent::init();
    }

}
