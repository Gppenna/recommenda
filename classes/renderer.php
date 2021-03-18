<?php

namespace block_recommenda;

require_once("$CFG->libdir/formslib.php");

require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->libdir . '/coursecatlib.php');

class Renderer {

    public function __construct($interests = array(), $profile_interests = array())
    {
        $this->render_class = '';
    }

    private function validate_render($interests = array()) {
        if (empty($interests) || (!empty(optional_param('recommenda-editinterests-acao', '', PARAM_TEXT)) && optional_param('recommenda-editinterests-acao', '', PARAM_TEXT) == 'editinterests') || 
            (!empty(optional_param('block_recommenda-submitbutton', '', PARAM_TEXT)) && (empty($interests) && empty(optional_param_array('recommenda_tags', array(), PARAM_TEXT))))) {
            $this->render_class = '\\block_recommenda\\form\\editinterests';
        }
        else if(!empty($interests)) {
            $this->render_class = '\\block_recommenda\\carroussel\\carroussel';
        }
    }
}