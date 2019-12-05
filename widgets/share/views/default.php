<ul>
    <?php if ($this->context->facebook && $url) { ?>
        <li><a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?= $url; ?>" title="facebook"><i
                        class="icon-facebook"></i> Поделиться</a></li>
    <?php } ?>
    <?php if ($this->context->twitter && $name && $url) { ?>
        <li><a class="twitter" href="https://twitter.com/intent/tweet?text=<?= $name; ?>&url=<?= $url; ?>"
               title="twitter"><i class="icon-twitter"></i> Твитнуть</a></li>
    <?php } ?>
    <?php if ($this->context->pinterest && $name && $url) { ?>
        <li><a class="pinterest"
               href="https://pinterest.com/pin/create/button/?url=<?= $url; ?>&media=&description=<?= $name; ?>"
               title="pinterest"><i class="icon-pinterest"></i> pinterest</a>
        </li>
    <?php } ?>
    <?php if ($this->context->linkedin && $name && $url) { ?>
        <li><a class="linkedin"
               href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url; ?>&title=<?= $name; ?>&summary=<?= $name; ?>&source="
               title="linkedin"><i class="icon-linkedin"></i> linkedin</a>
        </li>
    <?php } ?>
</ul>

