<?php

namespace Altum\Models;

use Altum\Uploads;

defined('ALTUMCODE') || die();

class Flipbook extends Model {

    public function delete($flipbook_id) {

        if(!$flipbook = db()->where('flipbook_id', $flipbook_id)->getOne('flipbooks', ['user_id', 'flipbook_id', 'source', 'link_id'])) {
            return;
        }

        /* Delete the stored pdf file */
        Uploads::delete_uploaded_file($flipbook->source, 'flipbooks');

        /* Delete the related link */
        (new Link())->delete($flipbook->link_id);

        /* Delete from database */
        db()->where('flipbook_id', $flipbook_id)->delete('flipbooks');

        /* Update user flipbooks counter */
        db()->where('user_id', $flipbook->user_id)->update('users', ['flipbooks' => db()->inc(-1)]);

        /* Clear the cache */
        cache('flipbooks_total?user_id=' . $flipbook->user_id)->delete();
        cache('links_total?user_id=' . $flipbook->user_id)->delete();
    }
}