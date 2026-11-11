<?php if (!empty($success)): ?>
  <div role="status" class="bg-primary/10 text-primary text-sm p-4 rounded-xl border border-primary/20 flex items-center gap-2">
    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
    <span><?= e($success) ?></span>
  </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
  <div role="alert" class="bg-destructive/10 text-destructive text-sm p-4 rounded-xl border border-destructive/20 flex items-center gap-2">
    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
    <span><?= e($error) ?></span>
  </div>
<?php endif; ?>
