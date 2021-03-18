<?php

namespace block_recommenda\carroussel;

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/renderer.php');
require_once($CFG->dirroot . '/blocks/recommenda/locallib.php');

class Carroussel
{
    public function __construct($interests = array())
    {   
        $this->$final_array = array_keys($this->organize_interests($interests));
        
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
        return randomize_interests($best_matches);
    }
}
