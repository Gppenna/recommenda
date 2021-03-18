<?php

namespace block_recommenda\carroussel;

use coursecat_helper;
use core_tag_tag;
use core_course_category;
use course_in_list;
use html_writer;
use moodle_url;

class Carroussel
{
    private $final_array;
    public function __construct($interests = array())
    {
        $this->final_array = array_keys($this->organize_interests($interests));
    }

    public function get_final_array()
    {
        return $this->final_array;
    }

    private function randomize_interests($best_matches)
    {
        $i = 0;
        $value_flag = 0;
        $count_flag = 0;

        $keys = array_keys($best_matches);
        for ($iter = 0; $iter < sizeof($keys); $iter++) {
            if ($count_flag == 1 && $best_matches[$keys[$iter]] != $value_flag) {
                $count_flag = 0;
                $value_flag = 0;
            }
            if ($count_flag == 0 && $value_flag == 0) {
                $i = $iter;
                $count_flag = 1;
                $value_flag = $best_matches[$keys[$iter]];
            } else {
                if ($best_matches[$keys[$iter]] == $value_flag) {
                    $count_flag++;
                }

                if (($count_flag > 1 && ($iter + 1) == sizeof($keys))) {
                    $first_position = $i;
                    $last_position = $iter;

                    $first_part = array_slice($best_matches, 0, $first_position, true);
                    $shuffle_part = array_slice($best_matches, $first_position, $count_flag, true);
                    $rest_part = array_slice($best_matches, $iter + 1, sizeof($best_matches) - $count_flag, true);

                    $shuffle_part = shuffle_assoc($shuffle_part);

                    if ($first_position == 0)
                        $best_matches = $shuffle_part + $rest_part;
                    else
                        $best_matches = $first_part + $shuffle_part + $rest_part;
                } else if (($count_flag > 1 && $best_matches[$keys[$iter + 1]] != $value_flag)) {

                    $first_position = $i;
                    $last_position = $iter;

                    $first_part = array_slice($best_matches, 0, $first_position, true);
                    $shuffle_part = array_slice($best_matches, $first_position, $count_flag, true);
                    $rest_part = array_slice($best_matches, $iter + 1, sizeof($best_matches) - $count_flag, true);

                    $shuffle_part = shuffle_assoc($shuffle_part);

                    if ($first_position == 0)
                        $best_matches = $shuffle_part + $rest_part;
                    else
                        $best_matches = $first_part + $shuffle_part + $rest_part;

                    $count_flag = 0;
                    $value_flag = 0;
                }
            }
        }
        return $best_matches;
    }

    private function organize_interests($interests)
    {
        global $USER;


        $best_matches = array();

        foreach ($interests as $tag) {

            // Get tag object

            $interest_object = core_tag_tag::get_by_name(0, $tag, $returnfields = '*', $strictness = IGNORE_MISSING);

            // Search courses associated with certain tag

            $search_criteria = array('tagid' => $interest_object->id);
            $courses_array = core_course_category::search_courses($search_criteria);

            // Find top recommendations based on tags in common

            foreach ($courses_array as $course) {
                $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
                $enrolled = is_enrolled($context, $USER->id, '', true);

                if (!$enrolled) {
                    if (empty($best_matches)) {
                        $best_matches[$course->id] = 1;
                    } else {
                        if (empty($best_matches[$course->id])) {
                            $best_matches[$course->id] = 1;
                        } else {
                            $best_matches[$course->id]++;
                        }
                    }
                }
            }
        }
        uasort($best_matches, 'cmp');
        return $this->randomize_interests($best_matches);
    }

    public function render($final_array = array())
    {
        global $DB, $CFG;
        $temp_html = '';
        $chelper = new coursecat_helper();

        $temp_html .= '<div id = "myCarousel" class="carousel slide" data-ride = "carousel" data-interval="5000">';
        $temp_html .= '<div class="carousel-inner row w-100 mx-auto" role="listbox">';
        if (sizeof($final_array) == 0) {
            $this->content->text .= HTML_WRITER::tag('p', get_string('zerocourses', 'block_recommenda'), array('id' => 'enrolled-every-course'));
            return $this->content;
        }

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
                    $img_url = file_encode_url(
                        "{$CFG->wwwroot}/pluginfile.php",
                        '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                            $file->get_filearea() . $file->get_filepath() . $file->get_filename(),
                        !$is_image
                    );
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
}
