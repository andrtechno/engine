<div class="like <?= $this->context->id ?> clearfix btn btn-sm">
    <div class="float-left <?= $response['activeLike'] ? 'active' : ''; ?>">
        <a class="like-up" href="/like/up/<?= $object_id; ?>"
           title="Мне нравиться"
           data-toggle="tooltip"
           data-widget="<?= $this->context->id ?>"
           data-hash="<?= $this->context->hash ?>" onclick="return false;">
            <span class="count-like"><?= $response['likeCount'] ?></span>
        </a>
    </div>
    <div class="float-left <?= $response['activeDislike'] ? 'active' : ''; ?>">
        <a class="like-down" href="/like/down/<?= $object_id; ?>"
           title="Мне не нравиться"
           data-toggle="tooltip"
           data-widget="<?= $this->context->id ?>"
           data-hash="<?= $this->context->hash ?>" onclick="return false;">
            <span class="count-dislike"><?= $response['dislikeCount'] ?></span>
        </a>
    </div>
</div>