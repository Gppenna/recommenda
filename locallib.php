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
 * Recommenda block helper functions
 *
 * @package    block_recommenda
 * @subpackage block_recommenda
 * @copyright  2020 Gabriel Penna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/renderer.php');

function cmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

function refresh_interests() {
    global $USER;
    if (!empty(optional_param('block_recommenda-submitbutton', '', PARAM_TEXT))) {
        useredit_update_interests($USER, optional_param_array('recommenda_tags', array(), PARAM_TEXT));
    }
}

/////////////////////////////////////
// Shuffle function
/////////////////////////////////////

function shuffle_assoc($list) {
    if (!is_array($list))
        return $list;

    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)
        $random[$key] = $list[$key];

    return $random;
}

/////////////////////////////////////
// Print function
/////////////////////////////////////

function print_r2($val) {
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}
