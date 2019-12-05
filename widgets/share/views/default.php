<?php
use panix\engine\Html;

?>

<ul>
    <?php if ($this->context->facebook && $url) { ?>
        <li>
            <a class="share facebook"
               href="<?= Html::buildUrl('https://www.facebook.com/sharer/sharer.php', ['u' => $url]); ?>"
               title="facebook"><i
                        class="icon-facebook"></i> Поделиться</a>
        </li>
    <?php } ?>
    <?php if ($this->context->twitter && $name && $url) { ?>
        <li>
            <a class="share twitter"
               href="<?= Html::buildUrl('https://twitter.com/intent/tweet', ['text' => $name, 'url' => $url]); ?>"
               title="twitter"><i class="icon-twitter"></i> Твитнуть</a>
        </li>
    <?php } ?>
    <?php if ($this->context->pinterest && $name && $url && $image) { ?>
        <li>
            <a class="share pinterest"
               href="<?= Html::buildUrl('https://pinterest.com/pin/create/button/', ['description' => $name, 'url' => $url, 'media' => $image]); ?>"
               title="pinterest"><i class="icon-pinterest"></i> pinterest</a>
        </li>
    <?php } ?>
    <?php if ($this->context->linkedin && $name && $url) { ?>
        <li>
            <a class="share linkedin"
               href="<?= Html::buildUrl('https://www.linkedin.com/shareArticle', ['title' => $name, 'url' => $url, 'media' => $image, 'mini' => 'true', 'summary' => $name, 'source' => '']); ?>"
               title="linkedin"><i class="icon-linkedin"></i> linkedin</a>
        </li>
    <?php } ?>
</ul>
