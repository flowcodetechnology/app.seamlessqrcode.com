<?php

/* Load all the related plugin files */
require_once \Altum\Plugin::get('pwa')->path . 'Pwa.php';

/* Functions */
if(!function_exists('pwa_generate_manifest')) {
    function pwa_generate_manifest($options = []) {
        $manifest = [
            'id' => md5(SITE_URL),
            'start_url' => $options['start_url'] ?? SITE_URL,
            'scope' => SITE_URL,
            'name' => $options['name'],
            'short_name' => $options['short_name'],
            'description' => $options['description'],
            'theme_color' => $options['theme_color'],
            'background_color' => $options['theme_color'],
            'display' => 'standalone',
            'orientation' => 'any',
            'icons' => [],
            'screenshots' => [],
            'categories' => ['utilities'],
            'dir' => 'auto',
            'lang' => \Altum\Language::$default_code,
        ];

        if($options['app_icon_url']) {
            $manifest['icons'][] = [
                'src' => $options['app_icon_url'],
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any'
            ];
        }

        if($options['app_icon_maskable_url']) {
            $manifest['icons'][] = [
                'src' => $options['app_icon_maskable_url'],
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable'
            ];
        }

        if(count($options['mobile_screenshots'])) {
            foreach($options['mobile_screenshots'] as $screenshot_url) {
                $info = getimagesize($screenshot_url);

                $manifest['screenshots'][] = [
                    'src' => $screenshot_url,
                    'sizes' => $info[0] . 'x' . $info[1],
                    'type' => $info['mime'],
                    'form_factor' => 'narrow'
                ];
            }
        }

        if(count($options['desktop_screenshots'])) {
            foreach($options['desktop_screenshots'] as $screenshot_url) {
                $info = getimagesize($screenshot_url);

                $manifest['screenshots'][] = [
                    'src' => $screenshot_url,
                    'sizes' => $info[0] . 'x' . $info[1],
                    'type' => $info['mime'],
                    'form_factor' => 'wide'
                ];
            }
        }

        if(count($options['shortcuts'])) {
            foreach($options['shortcuts'] as $shortcut) {
                if(!empty($shortcut['name'])) {
                    $manifest['shortcuts'][] = [
                        'name' => $shortcut['name'],
                        'description' => $shortcut['description'],
                        'url' => $shortcut['url'],
                        'icons' => [[
                            'src' => $shortcut['icon_url'],
                            'sizes' => '192x192',
                        ]]
                    ];
                }
            }
        }

        return json_encode($manifest);
    }
}

if(!function_exists('pwa_save_manifest')) {
    function pwa_save_manifest($manifest_content) {
        file_put_contents(\Altum\Uploads::get_full_path('pwa') . 'manifest.json', $manifest_content);
    }
}
