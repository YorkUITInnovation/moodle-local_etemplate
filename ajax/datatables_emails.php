<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Datatables endpoint for email templates.
 *
 * @package    local_etemplate
 * @copyright  2026 Your Organization
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_helpers\datatables;

require_once('../../../config.php');

global $CFG, $DB, $USER;

$context = context_system::instance();
require_login(1, false);

// Get values from Datatables.
$draw = optional_param('draw', 1, PARAM_INT);
$start = optional_param('start', 0, PARAM_INT);
$length = optional_param('length', 25, PARAM_INT);
$deleted = optional_param('deleted', 0, PARAM_INT);
$bot_id = optional_param('bot_id', 0, PARAM_INT);

// Calculate actual limit end based on start and length values.
$end = $start + $length;
// Using $_REQUEST as optional_param_array was not working.
if (isset($_REQUEST['search'])) {
    $search = $_REQUEST['search'];
} else {
    $search = [];
}

if (isset($_REQUEST['order'])) {
    $order = $_REQUEST['order'];
} else {
    $order = [];
}

if (isset($_REQUEST['columns'])) {
    $columns = $_REQUEST['columns'];
} else {
    $columns = [];
}

// Set term value.
if (isset($search['value'])) {
    $term = $search['value'];
} else {
    $term = '';
}

// Get column to be sorted.
if (isset($order[0]['column'])) {
    $orderColumn = $columns[$order[0]['column']]['data'];
    $orderDirection = $order[0]['dir'];
} else {
    $orderColumn = 'name';
    $orderDirection = 'ASC';
}

// Set datatables parameters.
datatables::set_table('local_et_email');
datatables::set_query_params(['bot_id' => $bot_id]);
datatables::set_term($term);
datatables::set_order_column($orderColumn);
datatables::set_order_direction($orderDirection);
datatables::set_columns(['id', 'name']);
datatables::set_require_actions(true);
datatables::set_action_column('id');
datatables::set_action_class_name('entity');
datatables::set_start($start);
datatables::set_end($end);
datatables::set_action_item_buttons([
    0 => [
        'title' => get_string('keywords', 'local_cria'),
        'class' => 'btn btn-outline-primary btn-sm',
        'href' => '/local/cria/bot_keywords.php',
        'data-original-title' => '<i class="bi bi-key"></i>',
        'query_strings' => ['entity_id' => 'id']
    ]
]);
// Get results.
$data = datatables::get_records();

// Create datatables object.
$params = [
    'draw' => $draw,
    'recordsTotal' => $data->total_filtered,
    'recordsFiltered' => $data->total_found,
    'data' => $data->results,
];

// Return Datatables json object.
echo json_encode($params);
