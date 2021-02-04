<?php

namespace panix\engine\components;

use yii\helpers\Markdown;
use yii\base\ViewRenderer;

class MarkdownRenderer extends ViewRenderer
{
    public $format = 'gfm';

    public function render($view, $file, $params)
    {
        return Markdown::process(file_get_contents($file), $this->format);
    }
}