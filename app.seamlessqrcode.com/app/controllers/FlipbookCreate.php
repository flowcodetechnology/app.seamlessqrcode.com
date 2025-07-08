<?php

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Uploads;
use Altum\Models\Domain;

defined('ALTUMCODE') || die();

class FlipbookCreate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->flipbooks->is_enabled) {
            redirect('not-found');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.flipbooks')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('flipbooks');
        }

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `flipbooks` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;
        $flipbooks_limit = $this->user->plan_settings->flipbooks_limit ?? 0;

        if($flipbooks_limit != -1 && $total_rows >= $flipbooks_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('flipbooks');
        }

        /* Get available domains */
        $domains = (new Domain())->get_available_domains_by_user($this->user, true);

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Default settings */
        $settings = [
            'viewMode' => '3d',
            'singlePageView' => false,
            'skin' => 'dark',
            'page_shadows' => true,
            'sound' => true,
            'fullscreen' => true,
            'thumbnails' => true,
            'table_of_contents' => true,
            'share' => true,
            'zoom' => true,
            'print' => true,
            'download' => true,
            'custom_branding' => [
                'name' => '',
                'url' => ''
            ]
        ];

        if(!empty($_POST)) {
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['url'] = !empty($_POST['url']) && $this->user->plan_settings->custom_url ? get_slug(trim($_POST['url'])) : false;
            $_POST['domain_id'] = isset($_POST['domain_id']) && isset($domains[$_POST['domain_id']]) ? (int) $_POST['domain_id'] : 0;
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }
            
            if(empty($_POST['name'])) {
                Alerts::add_field_error('name', l('global.error_message.empty_field'));
            }
            
            if(empty($_FILES['source']['name'])) {
                Alerts::add_field_error('source', l('global.error_message.empty_field'));
            } else {
                $file_name = $_FILES['source']['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES['source']['tmp_name'];
                $flipbook_max_size_mb = $this->user->plan_settings->flipbook_max_size_mb ?? 0;

                if($_FILES['source']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), settings()->main->max_file_size_mb));
                }

                if(!in_array($file_extension, ['pdf'])) {
                    Alerts::add_field_error('source', l('global.error_message.invalid_file_type'));
                }

                if($flipbook_max_size_mb != -1 && ($_FILES['source']['size'] > $flipbook_max_size_mb * 1024 * 1024)) {
                    Alerts::add_field_error('source', sprintf(l('global.error_message.file_size_limit'), $flipbook_max_size_mb));
                }
            }

            /* Check for duplicate url if needed */
            if($_POST['url']) {
                if(db()->where('url', $_POST['url'])->where('domain_id', $_POST['domain_id'])->has('links')) {
                    Alerts::add_field_error('url', l('link.error_message.url_exists'));
                }
            }

            if(empty($_POST['url'])) {
                $_POST['url'] = string_generate(10);
                while(db()->where('url', $_POST['url'])->where('domain_id', $_POST['domain_id'])->has('links')) {
                    $_POST['url'] = string_generate(10);
                }
            }

            /* Settings */
            $settings['viewMode'] = $_POST['view_mode'] = in_array($_POST['view_mode'], ['3d', '2d', 'swipe', 'scroll']) ? $_POST['view_mode'] : '3d';
            $settings['singlePageView'] = $_POST['single_page_view'] = isset($_POST['single_page_view']);
            $settings['skin'] = $_POST['skin'] = in_array($_POST['skin'], ['dark', 'light', 'gradient']) ? $_POST['skin'] : 'dark';
            $settings['page_shadows'] = $_POST['page_shadows'] = isset($_POST['page_shadows']);
            $settings['sound'] = $_POST['sound'] = isset($_POST['sound']);
            $settings['fullscreen'] = $_POST['fullscreen'] = isset($_POST['fullscreen']);
            $settings['thumbnails'] = $_POST['thumbnails'] = isset($_POST['thumbnails']);
            $settings['table_of_contents'] = $_POST['table_of_contents'] = isset($_POST['table_of_contents']);
            $settings['share'] = $_POST['share'] = isset($_POST['share']);
            $settings['zoom'] = $_POST['zoom'] = isset($_POST['zoom']);
            $settings['print'] = $_POST['print'] = isset($_POST['print']);
            $settings['download'] = $_POST['download'] = isset($_POST['download']);

            if(isset($this->user->plan_settings->enabled_flipbook_custom_branding) && $this->user->plan_settings->enabled_flipbook_custom_branding) {
                $settings['custom_branding']['name'] = input_clean($_POST['custom_branding_name'], 64);
                $settings['custom_branding']['url'] = input_clean($_POST['custom_branding_url'], 512);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Prepare the filename and path */
                $file_new_name = md5(time() . rand()) . '.' . $file_extension;
                $file_path = Uploads::get_path('flipbooks') . $file_new_name;

                /* Upload the file */
                move_uploaded_file($file_temp, $file_path);

                $settings_json = json_encode($settings);

                /* Create the link for the flipbook */
                $url = $_POST['url'];
                $location_url = url('f/' . $url);
                $link_id = db()->insert('links', [
                    'user_id' => $this->user->user_id,
                    'domain_id' => $_POST['domain_id'],
                    'type' => 'flipbook',
                    'url' => $url,
                    'location_url' => $location_url,
                    'settings' => json_encode(['clicks_limit' => null, 'expiration_url' => null, 'password' => null, 'sensitive_content' => false]),
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Database query */
                db()->insert('flipbooks', [
                    'user_id' => $this->user->user_id,
                    'link_id' => $link_id,
                    'project_id' => $_POST['project_id'],
                    'name' => $_POST['name'],
                    'url' => $url,
                    'source' => $file_new_name,
                    'settings' => $settings_json,
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Update user flipbooks counter */
                db()->where('user_id', $this->user->user_id)->update('users', ['flipbooks' => db()->inc()]);

                /* Clear the cache */
                cache('flipbooks_total?user_id=' . $this->user->user_id)->delete();

                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));
                redirect('flipbook-update/' . $link_id);
            }
        }

        /* Set default values */
        $values = [
            'name' => $_POST['name'] ?? '',
            'url' => $_POST['url'] ?? '',
            'project_id' => $_POST['project_id'] ?? null,
            'settings' => $settings,
        ];

        /* Prepare the view */
        $data = [
            'projects' => $projects,
            'domains' => $domains,
            'values' => $values,
        ];

        $view = new \Altum\View('flipbook-create/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }
}