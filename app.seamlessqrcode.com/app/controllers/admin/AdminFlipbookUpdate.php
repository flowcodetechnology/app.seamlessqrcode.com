<?php

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Uploads;

defined('ALTUMCODE') || die();

class AdminFlipbookUpdate extends Controller {

    public function index() {

        $flipbook_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$flipbook = db()->where('flipbook_id', $flipbook_id)->getOne('flipbooks')) {
            redirect('admin/flipbooks');
        }

        $flipbook->settings = json_decode($flipbook->settings);
        $user = (new \Altum\Models\User())->get_user_by_user_id($flipbook->user_id);

        if(!empty($_POST)) {
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['url'] = !empty($_POST['url']) ? get_slug(query_clean($_POST['url'])) : $flipbook->url;

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(empty($_POST['name'])) {
                Alerts::add_field_error('name', l('global.error_message.empty_field'));
            }
            if($flipbook->url != $_POST['url'] && db()->where('url', $_POST['url'])->has('links')) {
                Alerts::add_field_error('url', l('links.error_message.url_exists'));
            }
            if($flipbook->url != $_POST['url'] && db()->where('url', $_POST['url'])->has('flipbooks')) {
                Alerts::add_field_error('url', l('links.error_message.url_exists'));
            }

            /* Settings */
            $settings = (array) $flipbook->settings;
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

            if($user->plan_settings->enabled_flipbook_custom_branding) {
                $settings['custom_branding']['name'] = input_clean($_POST['custom_branding_name'], 64);
                $settings['custom_branding']['url'] = input_clean($_POST['custom_branding_url'], 512);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $settings_json = json_encode($settings);

                /* Update the link for the flipbook */
                $link_id = $flipbook->link_id;
                $url = $_POST['url'];
                $location_url = url('f/' . $url);
                db()->where('link_id', $link_id)->update('links', ['url' => $url, 'location_url' => $location_url]);

                /* Database query */
                db()->where('flipbook_id', $flipbook_id)->update('flipbooks', [
                    'name' => $_POST['name'],
                    'url' => $url,
                    'settings' => $settings_json,
                    'last_datetime' => \Altum\Date::$date,
                ]);

                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));
                redirect('admin/flipbook-update/' . $flipbook_id);
            }
        }

        /* Main View */
        $data = [
            'flipbook' => $flipbook,
            'user' => $user
        ];

        $view = new \Altum\View('admin/flipbook-update/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }
}