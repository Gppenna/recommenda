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

/////////////////////////////////////
// Randomize courses with same rank
/////////////////////////////////////

/*function randomize_interests($best_matches) {
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

/////////////////////////////////////
// Rank the courses and organize them
/////////////////////////////////////

function organize_interests($interests) {
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
*/