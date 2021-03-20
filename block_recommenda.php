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

require_once($CFG->dirroot . '/blocks/recommenda/locallib.php');

require_once($CFG->dirroot . '/user/editlib.php');


class block_recommenda extends block_base
{

    public function init()
    {
        $this->title = get_string('recommenda', 'block_recommenda');
    }

    function has_config()
    {
        return true;
    }

    public function get_content()
    {
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
        $this->content->text .= '
    		<script src="https://cdnjs.cloudflare.com/ajax/libs/eqcss/1.9.2/EQCSS.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/css-element-queries/1.2.3/ResizeSensor.js"></script>';

        refresh_interests();
        
        $path = '\\block_recommenda\\validate';
        $validate = new $path();
        $class = $validate->get_class();
        $class_object = new $class($validate->get_interests());

        $html_content = $class_object->render($class_object->get_final_array());
        $this->content->text .= $html_content;
        
        $PAGE->requires->jquery();
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/blocks/recommenda/js/module.js'));
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/blocks/recommenda/js/dotdotdot.js'));

        return $this->content;
    }
}
