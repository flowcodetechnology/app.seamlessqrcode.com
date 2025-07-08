<?php defined('ALTUMCODE') || die() ?>

<!DOCTYPE html>
<html lang="<?= \Altum\Language::$code ?>" class="h-100">
<head>
    <title><?= \Altum\Meta::get_title() ?></title>
    <base href="<?= SITE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php if(!empty(settings()->favicon)): ?>
        <link href="<?= UPLOADS_FULL_URL . 'favicon/' . settings()->favicon ?>" rel="shortcut icon" />
    <?php endif ?>

    <link href="<?= ASSETS_FULL_URL . 'css/bootstrap.min.css' ?>" rel="stylesheet" media="screen,print">
    <link href="<?= ASSETS_FULL_URL . 'css/custom.css' ?>" rel="stylesheet" media="screen,print">
    <link href="<?= ASSETS_FULL_URL . 'css/pixel.css' ?>" rel="stylesheet" media="screen,print">
    
    <!-- Real3D Flipbook Assets -->
    <link href="<?= ASSETS_FULL_URL . 'real3d-flipbook/css/flipbook.min.css' ?>" rel="stylesheet" type="text/css">
    
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        #flipbook-container {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <div id="flipbook-container"></div>
    
    <?php $flipbook_settings = json_decode($data->flipbook->settings); ?>

    <script src="<?= ASSETS_FULL_URL . 'js/libraries/jquery.min.js' ?>"></script>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/bootstrap.min.js' ?>"></script>
    <script src="<?= ASSETS_FULL_URL . 'js/custom.js' ?>"></script>

    <!-- Real3D Flipbook Assets -->
    <script src="<?= ASSETS_FULL_URL . 'real3d-flipbook/js/flipbook.min.js' ?>"></script>
    
    <script type="text/javascript">
        $(document).ready(function () {
            let options = {
                // PDF file path
                pdf: '<?= UPLOADS_FULL_URL . 'flipbooks/' . $data->flipbook->source ?>',

                // Real3D Flipbook general options
                viewMode: '<?= $flipbook_settings->viewMode ?>',
                singlePageMode: <?= $flipbook_settings->singlePageView ? 'true' : 'false' ?>,
                
                // Real3D Flipbook UI options
                skin: '<?= $flipbook_settings->skin ?>',
                pageShadows: <?= $flipbook_settings->page_shadows ? 'true' : 'false' ?>,
                sound: <?= $flipbook_settings->sound ? 'true' : 'false' ?>,

                // Toolbar buttons
                btnFullscreen: { enabled: <?= $flipbook_settings->fullscreen ? 'true' : 'false' ?> },
                btnToc: { enabled: <?= $flipbook_settings->table_of_contents ? 'true' : 'false' ?> },
                btnShare: { enabled: <?= $flipbook_settings->share ? 'true' : 'false' ?> },
                btnDownloadPdf: { enabled: <?= $flipbook_settings->download ? 'true' : 'false' ?> },
                btnPrint: { enabled: <?= $flipbook_settings->print ? 'true' : 'false' ?> },
                btnZoom: { enabled: <?= $flipbook_settings->zoom ? 'true' : 'false' ?> },
                btnThumbs: { enabled: <?= $flipbook_settings->thumbnails ? 'true' : 'false' ?> },

                // Path to PDF.js worker
                pdfjsSrc: '<?= ASSETS_FULL_URL . 'real3d-flipbook/js/libs/pdf.worker.min.js' ?>',

                // Assets path
                assets: {
                    preloader: '<?= ASSETS_FULL_URL . 'real3d-flipbook/assets/images/preloader.jpg' ?>',
                    left: '<?= ASSETS_FULL_URL . 'real3d-flipbook/assets/images/left.png' ?>',
                    right: '<?= ASSETS_FULL_URL . 'real3d-flipbook/assets/images/right.png' ?>',
                    flip: '<?= ASSETS_FULL_URL . 'real3d-flipbook/assets/mp3/turn_page.mp3' ?>'
                },

                <?php if($data->user->plan_settings->enabled_flipbook_custom_branding && !empty($flipbook_settings->custom_branding->name)): ?>
                logo: {
                    url: '<?= $flipbook_settings->custom_branding->url ?? '#' ?>',
                    title: '<?= $flipbook_settings->custom_branding->name ?>'
                }
                <?php endif ?>
            };
            
            $('#flipbook-container').flipBook(options);
        });
    </script>

</body>
</html>