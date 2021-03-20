<?php

namespace block_recommenda;

class validate
{

    private $render_class;
    public function __construct($interests = array(), $profile_interests = array())
    {
        $this->total_interests = $interests;
        $this->partial_interests = $profile_interests;
        $this->final_interests = $this->validate_render($interests);
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
            $tmp_int = $this->partial_interests;
        } else if (!empty($interests)) {
            $this->render_class = '\\block_recommenda\\carroussel\\carroussel';
            $tmp_int = $this->total_interests;
        }
        return $tmp_int;
    }
}
