<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 m-0"><i class="fa fa-fw fa-xs fa-book-open text-primary-900 mr-2"></i> <?= l('admin_flipbooks.header') ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="tooltip" title="<?= l('global.export') ?>" onclick="$('#export_modal').modal('show');">
                <i class="fa fa-fw fa-sm fa-download"></i>
            </button>
            <div class="ml-3">
                <button id="bulk_enable" type="button" class="btn btn-sm btn-outline-secondary" data-toggle="tooltip" title="<?= l('global.bulk_actions') ?>" onclick="$('#bulk_actions').toggle();">
                    <i class="fa fa-fw fa-sm fa-list-ul"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="bulk_actions" class="mb-4" style="display: none;">
    <form action="<?= url('admin/flipbooks/bulk') ?>" method="post" role="form">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="form-group">
                    <label for="bulk_action"><?= l('global.bulk_actions') ?></label>
                    <select id="bulk_action" name="type" class="form-control form-control-sm">
                        <option value="delete"><?= l('global.delete') ?></option>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn btn-sm btn-primary"><?= l('global.submit') ?></button>
            </div>
        </div>
    </form>
</div>

<form id="table" action="<?= SITE_URL . 'admin/flipbooks' ?>" method="get" role="form">
    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th class="d-none d-md-table-cell">
                    <div class="custom-control custom-checkbox">
                        <input id="select_all" type="checkbox" class="custom-control-input" />
                        <label for="select_all" class="custom-control-label"></label>
                    </div>
                </th>
                <th><?= l('global.user') ?></th>
                <th><?= l('flipbooks.table.name') ?></th>
                <th><?= l('flipbooks.table.url') ?></th>
                <th><?= l('flipbooks.page_views') ?></th>
                <th><?= l('global.datetime') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->flipbooks as $row): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->user_email = 'hidden@demo.com'; $row->user_name = 'hidden on demo';} ?>
                <tr>
                    <td class="d-none d-md-table-cell">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_flipbook_id_<?= $row->flipbook_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->flipbook_id ?>" />
                            <label for="selected_flipbook_id_<?= $row->flipbook_id ?>" class="custom-control-label"></label>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <div class="d-flex flex-column">
                            <div>
                                <a href="<?= url('admin/user-view/' . $row->user_id) ?>"><?= $row->user_name ?></a>
                            </div>
                            <span class="text-muted"><?= $row->user_email ?></span>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <a href="<?= url('admin/flipbook-update/' . $row->flipbook_id) ?>"><?= $row->name ?></a>
                    </td>

                    <td class="text-nowrap">
                        <a href="<?= $row->full_url ?>" target="_blank" rel="noreferrer"><?= $row->url ?></a>
                    </td>
                    
                    <td class="text-nowrap">
                        <?= nr($row->page_views) ?>
                    </td>

                    <td class="text-nowrap">
                        <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                            <?= \Altum\Date::get($row->datetime, 2) ?>
                        </span>
                    </td>

                    <td>
                        <div class="d-flex justify-content-end">
                            <?= include_view(THEME_PATH . 'views/admin/flipbooks/admin_flipbook_dropdown_button.php', ['id' => $row->flipbook_id, 'resource_name' => $row->name]) ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</form>

<div class="mt-3"><?= $data->pagination ?></div>

<?php require THEME_PATH . 'views/admin/partials/transfer_modal.php' ?>
<?php require THEME_PATH . 'views/partials/js_bulk.php' ?>