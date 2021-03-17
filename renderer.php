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

function get_all_tags($sort = '', $fields = '*', $limitfrom = 0, $limitnum = 0) {
    global $DB;

    $all_tags = $DB->get_records('tag', null, $sort, $fields, $limitfrom, $limitnum);
    return $all_tags;
}

function execute_interests_form($interests = array()) {
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
    if (!empty(optional_param('submitbutton', '', PARAM_TEXT)) && empty(optional_param_array('tags', array(), PARAM_TEXT))) {
        $html .= html_writer::tag('p', get_string('formerror', 'block_recommenda') . html_writer::tag('br', ''), array('style' => 'color: red'));
    }

    $count = 1;
    $col = 1;
    $countItens = count($all_tags);
    $modItens = $countItens % 3;
    $divItens = floor( $countItens / 3 );
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
        $html .= '<input ' . $checked . ' type="checkbox" name="tags[]" class="form-check-input" value="' . $tag->rawname . '" id="id_tag_' . $tag->id . '">';
        $html .= $tag->rawname . '</label>';
        $html .= '</div>';

        if ($count == ($divItens + (($modItens > 0 && $col <= $modItens)? 1 : 0)) || $boolLastKey) {
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
    $html .= '<input type="submit" class="btn btn-primary" name="submitbutton" id="id_submitbutton" value="' . get_string('submit', 'block_recommenda') . '">';
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

function render_html_block($final_array) {
    global $USER, $DB, $CFG, $OUTPUT;

    $temp_html = '';
    $chelper = new coursecat_helper();

    $temp_html .= '<div id = "myCarousel" class="carousel slide" data-ride = "carousel" data-interval="5000">';


    $temp_html .= '<div class="carousel-inner row w-100 mx-auto" role="listbox">';

    foreach ($final_array as $flag_key => $courseid) {
        if ($flag_key == 0) {
            $temp_html .= '<div class="carousel-item active col-12 col-sm-6 col-md-4">';
        } else {
            $temp_html .= '<div class="carousel-item active col-12 col-sm-6 col-md-4">';
        }
        $valid_counter = $flag_key + 1;
        $content = $content_images = $content_files = '';

        $course_final = $DB->get_record('course', array('id' => $courseid));

        $course_formatted = new course_in_list($course_final);

        $mobilecourselink = html_writer::link(new moodle_url('/course/view.php', array('id' => $courseid)), $course_final->fullname, array('class' => $course_final->visible ? '' : 'dimmed'));
        $content .= HTML_WRITER::tag('div', $mobilecourselink, array('class' => 'mobile-coursename', 'title' => $course_final->fullname));

        if (empty($course_formatted->get_course_overviewfiles())) {
            $img_url = new moodle_url('/blocks/recommenda/img/default-placeholder.png');
            $content .= html_writer::start_div('courseimage img-fluid mx-auto min-dimensions', array('style' => 'background: url("' . $img_url . '");', 'id' => $valid_counter));


            $content .= html_writer::start_div('courseimage-overlay img-fluid mx-auto min-dimensions');
        } else
            foreach ($course_formatted->get_course_overviewfiles() as $file) {
                $is_image = $file->is_valid_image();
                $img_url = file_encode_url("{$CFG->wwwroot}/pluginfile.php",
                        '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                        $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$is_image);
                if ($is_image) {
                    $content .= html_writer::start_div('courseimage img-fluid mx-auto min-dimensions', array('style' => 'background: url("' . $img_url . '");', 'id' => $valid_counter));

                    $content .= html_writer::start_div('courseimage-overlay img-fluid mx-auto min-dimensions');
                }
                $content .= $content_images . $content_files;
            }

        $courselink = html_writer::link(new moodle_url('/course/view.php', array('id' => $courseid)), $course_final->fullname, array('class' => $course_final->visible ? '' : 'dimmed'));

        if ($course_formatted->has_summary()) {
            $summary_string = $DB->get_field("course", "summary", array("id" => $course_final->id));
            $summary_string = format_string($summary_string);

            $content .= html_writer::start_div('coursename-overlay');
            $content .= HTML_WRITER::tag('p', $courselink, array('title' => $course_final->fullname));
            $content .= html_writer::end_div();

            $content .= '<div class="summary-overlay"><p>';
            $content .= $summary_string;
            $content .= '</p></div>';

            $content .= html_writer::end_div();
        }
        $content .= html_writer::start_div('coursename-container');
        $content .= HTML_WRITER::tag('p', $courselink, array('title' => $course_final->fullname));
        $content .= html_writer::end_div();

        $content .= html_writer::end_div();
        $temp_html .= $content . '</div>';
    }

    $temp_html .= '</div><a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
<span class="carousel-control-prev-icon" aria-hidden="true"></span>
<span class="sr-only">Previous</span>
</a>
<a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
<span class="carousel-control-next-icon" aria-hidden="true"></span>
<span class="sr-only">Next</span>
</a></div>';

    $EQCSS = "<script type=text/eqcss>

@element .block_recommenda and (min-width: 720px) {
  /* Show 4th slide on md  if col-md-4*/
    .block_recommenda .carousel-inner .active.col-md-4.carousel-item + .carousel-item + .carousel-item + .carousel-item{
        position: absolute;
        top: 0;
        right: -33.3333%;  /*change this with javascript in the future*/
        z-index: 1;
        display: block;
        visibility: visible;
    }

}

@element .block_recommenda and (min-width: 529px) and (max-width: 720px) {
  /* Show 3rd slide on sm  if col-sm-6*/
    .block_recommenda .carousel-inner .active.col-sm-6.carousel-item + .carousel-item + .carousel-item {
        position: absolute;
        top: 0;
        right: -50%;  /*change this with javascript in the future*/
        z-index: 1;
        display: block;
        visibility: visible;
    }

}

@element .block_recommenda and (min-width: 529px) {
    
    .block_recommenda .carousel-item {
        margin-right: 0;
    }

    /* show 2 items */
    .block_recommenda .carousel-inner .active + .carousel-item {
        display: block;
    }
    
    .block_recommenda .carousel-inner .carousel-item.active:not(.carousel-item-right):not(.carousel-item-left),
    .block_recommenda .carousel-inner .carousel-item.active:not(.carousel-item-right):not(.carousel-item-left) + .carousel-item {
        transition: none;
    }

    .block_recommenda .carousel-inner .carousel-item-next {
      position: relative;
      transform: translate3d(0, 0, 0);
    }
    
    /* left or forward direction */
    .block_recommenda .active.carousel-item-left + .carousel-item-next.carousel-item-left,
    .block_recommenda .carousel-item-next.carousel-item-left + .carousel-item,
    .block_recommenda .carousel-item-next.carousel-item-left + .carousel-item + .carousel-item {
        position: relative;
        transform: translate3d(-100%, 0, 0);
        visibility: visible;
    } 
    
    /* farthest right hidden item must be abso position for animations */
    .block_recommenda .carousel-inner .carousel-item-prev.carousel-item-right {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        display: block;
        visibility: visible;
    }
    
    /* right or prev direction */
    .block_recommenda .active.carousel-item-right + .carousel-item-prev.carousel-item-right,
    .block_recommenda .carousel-item-prev.carousel-item-right + .carousel-item,
    .block_recommenda .carousel-item-prev.carousel-item-right + .carousel-item + .carousel-item {
        position: relative;
        transform: translate3d(100%, 0, 0);
        visibility: visible;
        display: block;
        visibility: visible;
    }

}

/*MD*/
@element .block_recommenda and (min-width: 720px) {

    /* show 3rd of 3 item slide */
    .block_recommenda .carousel-inner .active + .carousel-item + .carousel-item {
        display: block;
    }
 
    .block_recommenda .carousel-inner .carousel-item.active:not(.carousel-item-right):not(.carousel-item-left) + .carousel-item + .carousel-item {
        transition: none;
    }
  
    
    .block_recommenda .carousel-inner .carousel-item-next {
      position: relative;
      transform: translate3d(0, 0, 0);
    }
    
    
    /* left or forward direction */
    .block_recommenda .carousel-item-next.carousel-item-left + .carousel-item + .carousel-item + .carousel-item {
        position: relative;
        transform: translate3d(-100%, 0, 0);
        visibility: visible;
    }
    
    /* right or prev direction */
    .block_recommenda .carousel-item-prev.carousel-item-right + .carousel-item + .carousel-item + .carousel-item {
        position: relative;
        transform: translate3d(100%, 0, 0);
        visibility: visible;
        display: block;
        visibility: visible;
    }

}

</script>";

    $temp_html .= $EQCSS;

    return $temp_html;
}
