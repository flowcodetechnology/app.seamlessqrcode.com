<?php

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Uploads;
use Altum\Models\Domain;

defined('ALTUMCODE') || die();

class FlipbookUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();
        
        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!settings()->flipbooks->is_enabled) {
            redirect('not-found');
        }

        if(!$flipbook = db()->where('link_id', $link_id)->where('user_id', $this->user->user_id)->getOne('flipbooks')) {
            redirect('flipbooks');
        }
        $link = db()->where('link_id', $link_id)->getOne('links', ['domain_id']);
        $flipbook->domain_id = $link->domain_id;

        $flipbook->settings = json_decode($flipbook->settings, true); // Decode as array

        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);
        $domains = (new Domain())->get_available_domains_by_user($this->user, true);
        
        if(!empty($_POST)) {
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['url'] = !empty($_POST['url']) && $this->user->plan_settings->custom_url ? get_slug(trim($_POST['url'])) : $flipbook->url;
            $_POST['domain_id'] = isset($_POST['domain_id']) && isset($domains[$_POST['domain_id']]) ? (int) $_POST['domain_id'] : $flipbook->domain_id;
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;

            if(!\Altum\Csrf::check()) { Alerts::add_error(l('global.error_message.invalid_csrf_token')); }

            $source = $flipbook->source;
            if(!empty($_FILES['source']['name'])) {
                $file_name = $_FILES['source']['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES['source']['tmp_name'];
                $flipbook_max_size_mb = $this->user->plan_settings->flipbook_max_size_mb ?? 0;

                if(!in_array($file_extension, ['pdf'])) { Alerts::add_field_error('source', l('global.error_message.invalid_file_type')); }
                if($flipbook_max_size_mb != -1 && ($_FILES['source']['size'] > $flipbook_max_size_mb * 1024 * 1024)) { Alerts::add_field_error('source', sprintf(l('global.error_message.file_size_limit'), $flipbook_max_size_mb)); }
                
                if(!Alerts::has_field_errors()) {
                    Uploads::delete_uploaded_file($flipbook->source, 'flipbooks');
                    $file_new_name = md5(time() . rand()) . '.' . $file_extension;
                    move_uploaded_file($file_temp, UPLOADS_PATH . Uploads::get_path('flipbooks') . $file_new_name);
                    $source = $file_new_name;
                }
            }

            if(empty($_POST['name'])) { Alerts::add_field_error('name', l('global.error_message.empty_field')); }
            
            if(($flipbook->url != $_POST['url'] || $flipbook->domain_id != $_POST['domain_id']) && db()->where('url', $_POST['url'])->where('domain_id', $_POST['domain_id'])->has('links')) {
                Alerts::add_field_error('url', l('link.error_message.url_exists'));
            }
            
            $settings = $flipbook->settings;
            $settings['viewMode'] = $_POST['view_mode'] ?? '3d';
            $settings['singlePageView'] = isset($_POST['single_page_view']);
            $settings['skin'] = $_POST['skin'] ?? 'dark';
            $settings['page_shadows'] = isset($_POST['page_shadows']);
            $settings['sound'] = isset($_POST['sound']);
            $settings['fullscreen'] = isset($_POST['fullscreen']);
            $settings['thumbnails'] = isset($_POST['thumbnails']);
            $settings['table_of_contents'] = isset($_POST['table_of_contents']);
            $settings['share'] = isset($_POST['share']);
            $settings['zoom'] = isset($_POST['zoom']);
            $settings['print'] = isset($_POST['print']);
            $settings['download'] = isset($_POST['download']);

            if(isset($this->user->plan_settings->enabled_flipbook_custom_branding) && $this->user->plan_settings->enabled_flipbook_custom_branding) {
                $settings['custom_branding']['name'] = input_clean($_POST['custom_branding_name'], 64);
                $settings['custom_branding']['url'] = input_clean($_POST['custom_branding_url'], 512);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $settings_json = json_encode($settings);
                $url = $_POST['url'];
                $location_url = url('f/' . $url);

                db()->where('link_id', $link_id)->update('links', [
                    'url' => $url, 'project_id' => $_POST['project_id'], 'domain_id' => $_POST['domain_id'],
                ]);
                
                db()->where('link_id', $link_id)->update('flipbooks', [
                    'project_id' => $_POST['project_id'], 'name' => $_POST['name'], 'url' => $url,
                    'source' => $source, 'settings' => $settings_json, 'last_datetime' => \Altum\Date::$date,
                ]);

                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . e($_POST['name']) . '</strong>'));
                redirect('flipbook-update/' . $link_id);
            }
        }

        $data = ['projects' => $projects, 'domains' => $domains, 'flipbook' => $flipbook];
        $view = new \Altum\View('flipbook-update/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }
}