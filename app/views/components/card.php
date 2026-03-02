<div class="card">
    <div class="card-title"><?= $__view->e($title) ?></div>
    <?php if (!empty($body)): ?>
        <div class="card-body"><?= $__view->e($body) ?></div>
    <?php endif; ?>
    <?php if (!empty($slot)): ?>
        <div class="card-footer"><?= $slot ?></div>
    <?php endif; ?>
</div>
