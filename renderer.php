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
 * Recommenda block html render functions
 *
 * @package    block_recommenda
 * @subpackage block_recommenda
 * @copyright  2020 Gabriel Penna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  
 */
require_once("$CFG->libdir/formslib.php");

require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->libdir . '/coursecatlib.php');

function get_all_tags($sort = '', $fields = '*', $limitfrom = 0, $limitnum = 0)
{
    global $DB;

    $all_tags = $DB->get_records('tag', null, $sort, $fields, $limitfrom, $limitnum);
    return $all_tags;
}

function execute_interests_form($interests = array())
{
    global $USER, $DB;

    $html = '';
    $html .= html_writer::start_div('form-h3');
    $initFormText  = get_string('forminit', 'block_recommenda') . ', ' . $USER->firstname . ' ' . $USER->lastname . '!' . html_writer::start_tag('br');
    $initFormText .= html_writer::start_span('text-smaller') . html_writer::start_tag('small') . get_string('formdesc', 'block_recommenda') . html_writer::end_tag('small') . html_writer::end_span() . html_writer::tag('br', '');
    $html .= html_writer::tag('h3', $initFormText);
    $html .= html_writer::end_div();
    $html .= html_writer::start_div('form-h4');
    $all_tags = get_all_tags('rawname');
    if (!empty($all_tags)) {
        $html .= html_writer::tag('h4', get_string('nointerests', 'block_recommenda') . html_writer::tag('br', ''));
    } else {
        $html .= html_writer::tag('h4', get_string('zerotags', 'block_recommenda') . html_writer::tag('br', ''));
    }
    $html .= html_writer::end_div();
    $html .= html_writer::start_div('form-items');

    $html .= '<form method="post" accept-charset="utf-8" id="form_tags" class="mform">';
    if (!empty(optional_param('block_recommenda-submitbutton', '', PARAM_TEXT)) && empty(optional_param_array('recommenda_tags', array(), PARAM_TEXT))) {
        $html .= html_writer::tag('p', get_string('formerror', 'block_recommenda') . html_writer::tag('br', ''), array('style' => 'color: red'));
    }

    $count = 1;
    $col = 1;
    $countItens = count($all_tags);
    $modItens = $countItens % 3;
    $divItens = floor($countItens / 3);
    $html .= '<div class="form-group row fitem">';
    foreach ($all_tags as $key => $tag) {
        if ($count == 1) {
            $html .= '<div class="col-md-4 form-item">';
        }
        $boolLastKey = ($key == array_key_last($all_tags));
        $html .= '<div class="form-group row fitem checkbox">';
        $checked = '';
        foreach ($interests as $user_tag_name) {
            if ($tag->rawname == $user_tag_name) {
                $checked = 'checked';
            }
        }
        $html .= '<label id="label_tag_' . $tag->id . '">';
        $html .= '<input ' . $checked . ' type="checkbox" name="recommenda_tags[]" class="form-check-input" value="' . $tag->rawname . '" id="id_tag_' . $tag->id . '">';
        $html .= $tag->rawname . '</label>';
        $html .= '</div>';

        if ($count == ($divItens + (($modItens > 0 && $col <= $modItens) ? 1 : 0)) || $boolLastKey) {
            $html .= '</div>';
            $count = 1;
            $col++;
        } else {
            $count++;
        }
    }
    $html .= '</div>';

    $html .= '<div id="fgroup_id_buttonar" class="form-group row fitem femptylabel form-inline" data-groupname="buttonar">';
    $html .= '<div class="col-md-9 offset-md-3">';
    $html .= '<div class="form-group fitem" style="display: inline;">';
    $html .= '<input type="submit" class="btn btn-primary" name="block_recommenda-submitbutton" id="block_recommenda-id_submitbutton" value="' . get_string('submit', 'block_recommenda') . '">';
    $html .= '</div>';
    if (!empty($interests)) {
        $html .= '<div class="form-group fitem" style="display: inline;">';
        $html .= '<input type="submit" class="btn btn-secondary" name="cancel" id="id_cancel" value="' . get_string('cancel', 'block_recommenda') . '">';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</form>';

    $html .= html_writer::end_div();
    return $html;
}

