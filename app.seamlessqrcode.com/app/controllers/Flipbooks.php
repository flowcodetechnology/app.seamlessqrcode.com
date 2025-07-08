<?php

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Models\Flipbook;

defined('ALTUMCODE') || die();

class Flipbooks extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->flipbooks->is_enabled) {
            redirect('not-found');
        }

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['project_id'], ['name'], ['datetime', 'last_datetime', 'name', 'page_views']));
        $filters->set_default_order_by('flipbook_id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `flipbooks` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('flipbooks?' . $filters->get_get() . '&page=%d')));

        /* Get the flipbooks list for the user */
        $flipbooks = [];
        $flipbooks_result = database()->query("
            SELECT 
                `flipbooks`.*, 
                `links`.`url` AS `full_url`, 
                `links`.`is_enabled`
            FROM `flipbooks` 
            LEFT JOIN `links` ON `flipbooks`.`link_id` = `links`.`link_id`
            WHERE `flipbooks`.`user_id` = {$this->user->user_id} 
            {$filters->get_sql_where('flipbooks')} 
            {$filters->get_sql_order_by('flipbooks')} 
            {$paginator->get_sql_limit()}
        ");

        /* Iterate over the results */
        while($row = $flipbooks_result->fetch_object()) {
            if($row->link_id && $link = db()->where('link_id', $row->link_id)->getOne('links', ['domain_id', 'url'])) {
                if($link->domain_id && $domain = (new \Altum\Models\Domain())->get_domain_by_id($link->domain_id)) {
                    $row->full_url = $domain->scheme . $domain->host . '/' . $link->url;
                } else {
                    $row->full_url = url('f/' . $link->url);
                }
            } else {
                $row->full_url = '#'; 
                $row->is_enabled = 0;
            }
            
            $flipbooks[] = $row;
        }


        /* Export handler */
        process_export_csv($flipbooks, 'include', ['flipbook_id', 'link_id', 'project_id', 'name', 'url', 'source', 'page_views', 'datetime', 'last_datetime'], sprintf(l('flipbooks.title')));
        process_export_json($flipbooks, 'include', ['flipbook_id', 'link_id', 'project_id', 'name', 'url', 'full_url', 'source', 'settings', 'page_views', 'datetime', 'last_datetime'], sprintf(l('flipbooks.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Prepare the view */
        $data = [
            'flipbooks'        => $flipbooks,
            'total_flipbooks'  => $total_rows,
            'pagination'       => $pagination,
            'filters'          => $filters,
            'projects'         => $projects,
        ];

        $view = new \Altum\View('flipbooks/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }

    public function delete() {
        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.flipbooks')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('flipbooks');
        }

        if(empty($_POST)) {
            redirect('flipbooks');
        }

        $flipbook_id = (int) query_clean($_POST['flipbook_id']);

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('flipbooks');
        }

        if(!$flipbook = db()->where('flipbook_id', $flipbook_id)->where('user_id', $this->user->user_id)->getOne('flipbooks', ['flipbook_id', 'name'])) {
            redirect('flipbooks');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
            (new Flipbook())->delete($flipbook->flipbook_id);
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $flipbook->name . '</strong>'));
            redirect('flipbooks');
        }

        redirect('flipbooks');
    }
}