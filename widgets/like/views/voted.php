<div class="like <?= $this->context->id ?>">
    <a class="like-up" href="/like/up/<?= $object_id; ?>" data-model="<?= get_class($this->context->model) ?>">like</a>
    <span class="like-counter"><?= $counter ?></span>
    <a class="like-down" href="/like/down/<?= $object_id; ?>" data-model="<?= get_class($this->context->model) ?>">dis like</a>
</div>