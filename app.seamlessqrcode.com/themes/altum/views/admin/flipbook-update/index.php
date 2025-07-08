<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-book-open text-primary-900 mr-2"></i> <?= l('flipbook_update.breadcrumb') ?></h1>
</div>

<div class="d-flex align-items-center mb-4">
    <img src="<?= get_gravatar($data->user->email) ?>" class="mr-3" />
    <div class="d-flex flex-column">
        <div>
            <a href="<?= url('admin/user-view/' . $data->user->user_id) ?>" class="font-weight-bold" target="_blank"><?= $data->user->name ?></a>
        </div>
        <span class="text-muted"><?= $data->user->email ?></span>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="name"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('flipbooks.table.name') ?></label>
                <input type="text" id="name" name="name" class="form-control" value="<?= $data->flipbook->name ?>" required="required" />
            </div>

            <div class="form-group">
                <label for="url"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('flipbooks.table.url') ?></label>
                <input type="text" id="url" name="url" class="form-control" value="<?= $data->flipbook->url ?>" placeholder="<?= l('global.url_placeholder') ?>" />
            </div>

            <div class="form-group">
                <label for="source"><i class="fa fa-fw fa-file-pdf fa-sm text-muted mr-1"></i> <?= l('flipbook_create.source') ?></label>
                <div class="mb-2">
                    <a href="<?= UPLOADS_FULL_URL . 'flipbooks/' . $data->flipbook->source ?>" target="_blank"><?= $data->flipbook->source ?></a>
                </div>
            </div>

            <h2 class="h5 mt-4"><?= l('flipbooks.settings') ?></h2>

            <div class="form-group">
                <label for="settings_view_mode"><?= l('flipbooks.settings.view_mode') ?></label>
                <select id="settings_view_mode" name="view_mode" class="form-control">
                    <option value="3d" <?= $data->flipbook->settings->viewMode == '3d' ? 'selected' : '' ?>><?= l('flipbooks.settings.view_mode_3d') ?></option>
                    <option value="2d" <?= $data->flipbook->settings->viewMode == '2d' ? 'selected' : '' ?>><?= l('flipbooks.settings.view_mode_2d') ?></option>
                    <option value="swipe" <?= $data->flipbook->settings->viewMode == 'swipe' ? 'selected' : '' ?>><?= l('flipbooks.settings.view_mode_swipe') ?></option>
                    <option value="scroll" <?= $data->flipbook->settings->viewMode == 'scroll' ? 'selected' : '' ?>><?= l('flipbooks.settings.view_mode_scroll') ?></option>
                </select>
            </div>
            
            <div class="form-group custom-control custom-switch">
                <input id="settings_single_page_view" name="single_page_view" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->singlePageView ? 'checked' : '' ?>>
                <label class="custom-control-label" for="settings_single_page_view"><?= l('flipbooks.settings.single_page_view') ?></label>
            </div>
            
            <div class="form-group">
                <label for="settings_skin"><?= l('flipbooks.settings.skin') ?></label>
                <select id="settings_skin" name="skin" class="form-control">
                    <option value="dark" <?= $data->flipbook->settings->skin == 'dark' ? 'selected' : '' ?>><?= l('flipbooks.settings.skin_dark') ?></option>
                    <option value="light" <?= $data->flipbook->settings->skin == 'light' ? 'selected' : '' ?>><?= l('flipbooks.settings.skin_light') ?></option>
                    <option value="gradient" <?= $data->flipbook->settings->skin == 'gradient' ? 'selected' : '' ?>><?= l('flipbooks.settings.skin_gradient') ?></option>
                </select>
            </div>

            <div class="form-group custom-control custom-switch">
                <input id="settings_page_shadows" name="page_shadows" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->page_shadows ? 'checked' : '' ?>>
                <label class="custom-control-label" for="settings_page_shadows"><?= l('flipbooks.settings.page_shadows') ?></label>
            </div>

            <div class="form-group custom-control custom-switch">
                <input id="settings_sound" name="sound" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->sound ? 'checked' : '' ?>>
                <label class="custom-control-label" for="settings_sound"><?= l('flipbooks.settings.sound') ?></label>
            </div>

            <p class="h6 mt-4"><?= l('global.options') ?></p>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group custom-control custom-switch">
                        <input id="settings_fullscreen" name="fullscreen" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->fullscreen ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="settings_fullscreen"><?= l('flipbooks.settings.fullscreen') ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group custom-control custom-switch">
                        <input id="settings_thumbnails" name="thumbnails" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->thumbnails ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="settings_thumbnails"><?= l('flipbooks.settings.thumbnails') ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group custom-control custom-switch">
                        <input id="settings_table_of_contents" name="table_of_contents" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->table_of_contents ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="settings_table_of_contents"><?= l('flipbooks.settings.table_of_contents') ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group custom-control custom-switch">
                        <input id="settings_share" name="share" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->share ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="settings_share"><?= l('flipbooks.settings.share') ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group custom-control custom-switch">
                        <input id="settings_zoom" name="zoom" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->zoom ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="settings_zoom"><?= l('flipbooks.settings.zoom') ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group custom-control custom-switch">
                        <input id="settings_print" name="print" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->print ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="settings_print"><?= l('flipbooks.settings.print') ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group custom-control custom-switch">
                        <input id="settings_download" name="download" type="checkbox" class="custom-control-input" <?= $data->flipbook->settings->download ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="settings_download"><?= l('flipbooks.settings.download') ?></label>
                    </div>
                </div>
            </div>

            <?php if($data->user->plan_settings->enabled_flipbook_custom_branding): ?>
                <div class="mt-4">
                    <p class="h5"><?= l('flipbooks.settings.custom_branding') ?></p>
                    <div class="form-group">
                        <label for="custom_branding_name"><?= l('flipbooks.settings.custom_branding.name') ?></label>
                        <input id="custom_branding_name" type="text" name="custom_branding_name" class="form-control" value="<?= $data->flipbook->settings->custom_branding->name ?? '' ?>" />
                    </div>
                    <div class="form-group">
                        <label for="custom_branding_url"><?= l('flipbooks.settings.custom_branding.url') ?></label>
                        <input id="custom_branding_url" type="text" name="custom_branding_url" class="form-control" value="<?= $data->flipbook->settings->custom_branding->url ?? '' ?>" />
                    </div>
                </div>
            <?php endif ?>

            <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= l('global.update') ?></button>
        </form>

    </div>
</div>