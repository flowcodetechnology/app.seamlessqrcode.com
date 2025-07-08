<?php defined('ALTUMCODE') || die() ?>

<div class="col-12 col-md-6 col-xl-4 mb-4">
    <div class="card h-100">
        <div class="card-body d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between">
                <h2 class="h5 m-0 card-title">
                    <a href="<?= url('flipbook-update/' . $data->flipbook->flipbook_id) ?>"><?= $data->flipbook->name ?></a>
                </h2>

                <div class="d-flex align-items-center">
                    <div class="custom-control custom-switch" data-toggle="tooltip" title="<?= l('links.is_enabled_tooltip') ?>">
                        <input
                                type="checkbox"
                                class="custom-control-input"
                                id="flipbook_is_enabled_<?= $data->flipbook->flipbook_id ?>"
                                data-row-id="<?= $data->flipbook->link_id ?>"
                                onchange="ajax_call_helper(event, 'links-ajax', 'is_enabled_toggle')"
                                <?= $data->flipbook->is_enabled ? 'checked="checked"' : null ?>
                        >
                        <label class="custom-control-label" for="flipbook_is_enabled_<?= $data->flipbook->flipbook_id ?>"></label>
                    </div>

                    <div class="dropdown">
                        <button type="button" class="btn btn-link text-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
                            <i class="fa fa-fw fa-ellipsis-v"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?= url('flipbook-update/' . $data->flipbook->flipbook_id) ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
                            <a class="dropdown-item" href="<?= url('link/' . $data->flipbook->link_id) ?>"><i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('link.statistics.link') ?></a>
                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#flipbook_delete_modal" data-flipbook-id="<?= $data->flipbook->flipbook_id ?>" data-resource-name="<?= e($data->flipbook->name) ?>"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                        </div>
                    </div>
                </div>

            </div>

            <p class="m-0">
                <small class="text-muted">
                    <i class="fa fa-fw fa-sm fa-link text-muted mr-1"></i>
                    <a href="<?= $data->flipbook->full_url ?>" target="_blank" rel="noreferrer"><?= remove_url_protocol_from_url($data->flipbook->full_url) ?></a>
                    <button
                            type="button"
                            class="btn btn-link btn-sm"
                            data-toggle="tooltip"
                            title="<?= l('global.clipboard_copy') ?>"
                            aria-label="<?= l('global.clipboard_copy') ?>"
                            data-copy="<?= l('global.clipboard_copy') ?>"
                            data-copied="<?= l('global.clipboard_copied') ?>"
                            data-clipboard-text="<?= $data->flipbook->full_url ?>"
                    >
                        <i class="fa fa-fw fa-sm fa-copy"></i>
                    </button>
                </small>
            </p>

            <p class="m-0">
                <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($data->flipbook->datetime, 1) ?>">
                    <i class="fa fa-fw fa-sm fa-calendar text-muted mr-1"></i>
                    <?= sprintf(l('global.datetime_heighlight'), \Altum\Date::get($data->flipbook->datetime, 2)) ?>
                </small>
            </p>
        </div>

        <div class="card-footer bg-gray-50 border-0">
            <div class="d-flex flex-lg-row justify-content-lg-between">
                <div>
                    <?php if($data->flipbook->project_id && isset($data->projects[$data->flipbook->project_id])): ?>
                        <a href="<?= url('flipbooks?project_id=' . $data->flipbook->project_id) ?>" class="text-muted">
                            <i class="fa fa-fw fa-sm fa-project-diagram text-muted mr-1"></i> <?= $data->projects[$data->flipbook->project_id]->name ?>
                        </a>
                    <?php else: ?>
                        <span class="text-muted">
                             <i class="fa fa-fw fa-sm fa-project-diagram text-muted mr-1"></i> <?= l('projects.no_project') ?>
                        </span>
                    <?php endif ?>
                </div>

                <div>
                    <a href="<?= url('link/' . $data->flipbook->link_id) ?>" class="text-muted" data-toggle="tooltip" title="<?= l('flipbooks.page_views') ?>">
                        <i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i>
                        <?= nr($data->flipbook->page_views) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>