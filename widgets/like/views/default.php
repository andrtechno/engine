<div class="like <?= $this->context->id ?> clearfix">

    <a class="like-up <?= $response['activeLike'] ? 'active' : ''; ?>" href="/like/up/<?= $object_id; ?>"
       title="<?= Yii::t('wgt_LikeWidget/default', 'LIKE'); ?>"
       data-toggle="tooltip"
       data-widget="<?= $this->context->id ?>"
       data-hash="<?= $this->context->hash ?>" onclick="return false;">
        <span class="count-like"><?= $response['likeCount'] ?></span>
    </a>

    <a class="like-down <?= $response['activeDislike'] ? 'active' : ''; ?>" href="/like/down/<?= $object_id; ?>"
       title="<?= Yii::t('wgt_LikeWidget/default', 'DISLIKE'); ?>"
       data-toggle="tooltip"
       data-widget="<?= $this->context->id ?>"
       data-hash="<?= $this->context->hash ?>" onclick="return false;">
        <span class="count-dislike"><?= $response['dislikeCount'] ?></span>
    </a>

</div>
