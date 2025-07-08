<?php

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Models\Flipbook;

defined('ALTUMCODE') || die();

class AdminFlipbooks extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'project_id'], ['name', 'url'], ['datetime', 'last_datetime', 'name', 'page_views']));
        $filters->set_default_order_by('flipbook_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `flipbooks` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/flipbooks?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $flipbooks = [];
        $flipbooks_result = database()->query("
            SELECT
                `flipbooks`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `flipbooks`
            LEFT JOIN
                `users` ON `flipbooks`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                {$paginator->get_sql_limit()}
        ");
        while($row = $flipbooks_result->fetch_object()) {
            $row->full_url = url('f/' . $row->url);
            $flipbooks[] = $row;
        }

        /* Export handler */
        process_export_csv($flipbooks, 'include', ['flipbook_id', 'user_id', 'project_id', 'name', 'url', 'source', 'page_views', 'datetime', 'last_datetime'], sprintf(l('flipbooks.title')));
        process_export_json($flipbooks, 'include', ['flipbook_id', 'user_id', 'project_id', 'name', 'url', 'full_url', 'source', 'settings', 'page_views', 'datetime', 'last_datetime'], sprintf(l('flipbooks.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'flipbooks' => $flipbooks,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('admin/flipbooks/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }


    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/flipbooks');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/flipbooks');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/flipbooks');
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':
                    foreach($_POST['selected'] as $flipbook_id) {
                        (new Flipbook())->delete($flipbook_id);
                    }
                    break;
            }

            Alerts::add_success(l('admin_bulk_delete_modal.success_message'));

        }

        redirect('admin/flipbooks');
    }

    public function delete() {
        $flipbook_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$flipbook = db()->where('flipbook_id', $flipbook_id)->getOne('flipbooks', ['flipbook_id', 'name'])) {
            redirect('admin/flipbooks');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
            (new Flipbook())->delete($flipbook_id);
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $flipbook->name . '</strong>'));
        }

        redirect('admin/flipbooks');
    }
}