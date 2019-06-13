<div class="like" id="<?= $this->id ?>">
    <a class=" like-up" href="/rate/up/<?= $object_id; ?>" data-csrf="<?= Yii::app()->request->csrfToken ?>" data-id="<?= $object_id; ?>" data-widget-id="<?= $this->id; ?>"><span class="icon-thumbs-up"></span></a>
    <span class="like-counter"><?= $counter ?></span>
    <a class=" like-down" href="/rate/down/<?= $object_id; ?>" data-csrf="<?= Yii::app()->request->csrfToken ?>" data-id="<?= $object_id; ?>" data-widget-id="<?= $this->id; ?>"><span class="icon-thumbs-up-2"></span></a>
    <div class="clear"></div>
</div>