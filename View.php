<?php

namespace panix\engine;

use Yii;

class View extends \yii\web\View {

    public function endPage($ajaxMode = false) {
        $this->trigger(self::EVENT_END_PAGE);
        $copyright = '<a href="//corner-cms.com/" id="corner" target="_blank"><span>' . Yii::t('app', 'CORNER') . '</span> &mdash; <span class="cr-logo">CORNER</span></a>';

        $content = ob_get_clean();

        $content = str_replace(base64_decode('e2NvcHlyaWdodH0='), $copyright, $content);
        echo strtr($content, [
            self::PH_HEAD => $this->renderHeadHtml(),
            self::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
            self::PH_BODY_END => $this->renderBodyEndHtml($ajaxMode),
        ]);

        $this->clear();
    }

}
