<div class="like <?= $this->context->id ?> clearfix">
    <div class="float-left"><a class="like-up <?= $response['activeLike']?'active':'';?>" href="/like/up/<?= $object_id; ?>" data-widget="<?= $this->context->id ?>" data-model="<?= get_class($this->context->model) ?>"><span class="count-like"><?= $response['likeCount'] ?></span></a></div>
    <div class="float-left"><a class="like-down <?= $response['activeDislike']?'active':'';?>" href="/like/down/<?= $object_id; ?>" data-widget="<?= $this->context->id ?>" data-model="<?= get_class($this->context->model) ?>"><span class="count-dislike"><?= $response['dislikeCount'] ?></span></a></div>
</div>
