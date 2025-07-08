<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link text-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fa fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="<?= url('admin/flipbook-update/' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
        <a href="#" data-toggle="modal" data-target="#transfer_modal" data-resource-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" data-resource-type="flipbook" class="dropdown-item"><i class="fa fa-fw fa-sm fa-random mr-2"></i> <?= l('admin_transfer_modal.menu') ?></a>
        <a href="#" data-toggle="modal" data-target="#resource_delete_modal" data-resource-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'flipbook',
    'resource_id' => 'flipbook_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/flipbooks/delete/' . $data->id . '?' . \Altum\Csrf::get_url_query()
]), 'modals'); ?>