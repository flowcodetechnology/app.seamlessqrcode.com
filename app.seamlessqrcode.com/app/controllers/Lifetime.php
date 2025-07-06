<?php
/*
 * Copyright (c) 2025 Leoivard (https://flowcode.co.ke/)
 */

namespace Altum\Controllers;

use Altum\Title;

defined('ALTUMCODE') || die();

class Lifetime extends Controller {

    public function index() {
        // ✅ Set the page title
        Title::set('Lifetime Plans');

        // ✅ Load all plans
        $all_plans = (new \Altum\Models\Plan())->get_plans();

        // ✅ Filter only active plans with valid lifetime pricing
        $lifetime_plans = array_filter($all_plans, function($plan) {
            return $plan->status == 1 &&
                   isset($plan->prices->lifetime->{currency()}) &&
                   $plan->prices->lifetime->{currency()} > 0;
        });

        // ✅ Pass only lifetime plans into the partial view
        $view = new \Altum\View('partials/plans', (array) $this);
        $this->add_view_content('plans', $view->run(['plans' => $lifetime_plans]));

        // ✅ Render the main lifetime page view
        $view = new \Altum\View('lifetime/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'type' => 'lifetime'
        ]));
    }
}
