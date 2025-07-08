<?php
namespace Altum\Models;
use Altum\Uploads;

defined('ALTUMCODE') || die();

class Flipbook extends Model {
    public function delete($flipbook_id) {
        if(!$flipbook = db()->where('flipbook_id', $flipbook_id)->getOne('flipbooks', ['user_id', 'flipbook_id', 'source'])) {
            return;
        }

        Uploads::delete_uploaded_file($flipbook->source, 'flipbooks');
        db()->where('flipbook_id', $flipbook_id)->delete('flipbooks');
        db()->where('user_id', $flipbook->user_id)->update('users', ['flipbooks' => db()->inc(-1)]);
        cache()->deleteItem('flipbooks_total?user_id=' . $flipbook->user_id);
    }
}