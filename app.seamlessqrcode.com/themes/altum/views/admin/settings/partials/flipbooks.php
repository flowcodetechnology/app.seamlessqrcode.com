<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group custom-control custom-switch">
        <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= settings()->flipbooks->is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="is_enabled"><i class="fas fa-fw fa-sm fa-book-open text-muted mr-1"></i> <?= l('admin_settings.flipbooks.is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.flipbooks.is_enabled_help') ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>