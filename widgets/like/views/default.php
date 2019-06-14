<div class="like <?= $this->context->id ?>">
    <a class="like-up" href="/like/up/<?= $object_id; ?>" data-widget="<?= $this->context->id ?>" data-model="<?= get_class($this->context->model) ?>">like</a>
    <span class="like-counter">
        <span class="count-like"><?= $response['likeCount'] ?></span>
         \ <span class="count-dislike"><?= $response['dislikeCount'] ?></span>
    </span>
    <a class="like-down" href="/like/down/<?= $object_id; ?>" data-widget="<?= $this->context->id ?>" data-model="<?= get_class($this->context->model) ?>">dis like</a>
</div>
