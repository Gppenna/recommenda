<?php

namespace block_recommenda;

use core_tag_tag;

class validate
{

    private $render_class;
    private $interests;
    private $profile_interests;
    private $final_interests;
    public function __construct()
    {
        $this->validate_interests(); 
        $this->final_interests = $this->validate_render($this->interests);
    }

    private function validate_interests() {
        global $USER;

        $this->profile_interests = core_tag_tag::get_item_tags_array('core', 'user', $USER->id, core_tag_tag::BOTH_STANDARD_AND_NOT, 0, false);
        $this->interests = $this->profile_interests;

        $courses_enrolled = array();
        $courses_enrolled = enrol_get_my_courses();

        foreach ($courses_enrolled as $course_enrolled_single) {
            $course_tags = core_tag_tag::get_item_tags_array('core', 'course', $course_enrolled_single->id);
            foreach ($course_tags as $key => $tag) {
                $this->interests[$key] = $tag;
            }
        }
    }

    public function get_class()
    {
        return $this->render_class;
    }

    public function get_interests()
    {
        return $this->final_interests;
    }

    private function validate_render($interests = array())
    {

        if (
            empty($interests) || (!empty(optional_param('recommenda-editinterests-acao', '', PARAM_TEXT)) && optional_param('recommenda-editinterests-acao', '', PARAM_TEXT) == 'recommenda-editinterests') ||
            (!empty(optional_param('block_recommenda-submitbutton', '', PARAM_TEXT)) && (empty($interests) && empty(optional_param_array('recommenda_tags', array(), PARAM_TEXT))))
        ) {
            $this->render_class = '\\block_recommenda\\form\\editinterests';
            $tmp_int = $this->profile_interests;
        } else if (!empty($interests)) {
            $this->render_class = '\\block_recommenda\\carroussel\\carroussel';
            $tmp_int = $this->interests;
        }
        return $tmp_int;
    }
}
