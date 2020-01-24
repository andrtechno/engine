<div id="scroll-to">
    <?php if ($this->context->enableTop) { ?>
        <div
        class="scroll-to top" <?php echo(($this->context->opacity > 0 && $this->context->opacity <= 1) ? '' : 'style="display:none;"'); ?>></div>
    <?php } ?>
    <?php if ($this->context->enableBottom) { ?>
        <div
        class="scroll-to bottom" <?php echo(($this->context->opacity > 0 && $this->context->opacity <= 1) ? '' : 'style="display:none;"'); ?>></div>
    <?php } ?>
</div>


