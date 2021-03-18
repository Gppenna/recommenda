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
 * Recommenda block definition
 *
 * @package    block_recommenda
 * @subpackage block_recommenda
 * @copyright  2020 Gabriel Penna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/recommenda/renderer.php');
require_once($CFG->dirroot . '/blocks/recommenda/locallib.php');

class block_recommenda extends block_base {

    public function init() {
        $this->title = get_string('recommenda', 'block_recommenda');
    }

    function has_config() {
        return true;
    }

    public function get_content() {
        global $PAGE, $USER, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $this->content->text = '';

        $this->content->text .= '<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">';
        $this->content->text .= '<script src="https://kit.fontawesome.com/ef79d3f4e6.js" crossorigin="anonymous"></script>';

        if (!empty(optional_param('block_recommenda-submitbutton', '', PARAM_TEXT))) {
            useredit_update_interests($USER, optional_param_array('recommenda_tags', array(), PARAM_TEXT));
        }
        
        // Array of user tags
        $profile_interests = core_tag_tag::get_item_tags_array('core', 'user', $USER->id, core_tag_tag::BOTH_STANDARD_AND_NOT, 0, false);
        $interests = array();
        $interests = $profile_interests;
        $courses_enrolled = array();
        $courses_enrolled = enrol_get_my_courses();

        foreach ($courses_enrolled as $course_enrolled_single) {
            $course_tags = core_tag_tag::get_item_tags_array('core', 'course', $course_enrolled_single->id);
            foreach ($course_tags as $key => $tag) {
                $interests[$key] = $tag;
            }
        }
        if (empty($interests) || (!empty(optional_param('recommenda-editinterests-acao', '', PARAM_TEXT)) && optional_param('recommenda-editinterests-acao', '', PARAM_TEXT) == 'recommenda-editinterests') || 
            (!empty(optional_param('block_recommenda-submitbutton', '', PARAM_TEXT)) && (empty($interests) && empty(optional_param_array('recommenda_tags', array(), PARAM_TEXT))))) {
            $this->content->text .= execute_interests_form($profile_interests);
        }
        else if (!empty($interests)) {
            $this->content->text = '';

            $path = '\\block_recommenda\\renderer';
            $carroussel = new $path($interests);

            $this->content->text .= html_writer::start_div('main-block');
            $this->content->text .= '<form id="form_edit_interests" method="post"><div class="relative-container">';
            $this->content->text .= '<input type="hidden" value="recommenda-editinterests" name="recommenda-editinterests-acao" >';
            $this->content->text .= '<a href="javascript:void(0)" class="recommenda-editinterests-acao btn btn-outline-secondary" onClick="document.getElementById(\'form_edit_interests\').submit();" title="'.get_string('editinterestsdesc', 'block_recommenda').'"><i class="fa fa-edit fa-1x"></i> '.get_string('editinterests', 'block_recommenda').'</a>';
            $this->content->text .= '</div></form>';
            $this->content->text .= '<div class="clear"></div>';
            $this->content->text .= '<div id="secondString">'.get_string('coursesshow2', 'block_recommenda').'</div>';
            $this->content->text .= '<div id="thirdString">'.get_string('coursesshow3', 'block_recommenda').'</div>';
            $final_array = array_keys(organize_interests($interests));
            if (sizeof($final_array) == 0) {
                $this->content->text .= HTML_WRITER::tag('p', get_string('zerocourses', 'block_recommenda'), array('id' => 'enrolled-every-course'));
                return $this->content;
            }
            $this->content->text .= render_html_block($final_array);

            $course_count = "1 " . get_string('coursesshow2', 'block_recommenda') . " " . count($final_array) . " " . get_string('coursesshow3', 'block_recommenda');

            $this->content->text .= html_writer::start_div('count-div');
            $this->content->text .= HTML_WRITER::tag('p', $course_count, array('id' => 'course-count'));
            $this->content->text .= html_writer::end_div();

            $this->content->text .= html_writer::end_div();
        }
        $this->content->text .= '
    		<script src="https://cdnjs.cloudflare.com/ajax/libs/eqcss/1.9.2/EQCSS.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/css-element-queries/1.2.3/ResizeSensor.js"></script>';
        $PAGE->requires->jquery();
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/blocks/recommenda/js/module.js'));
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/blocks/recommenda/js/dotdotdot.js'));

        return $this->content;
    }

}
