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
 * show activity dates.
 *
 * @package    theme-cul_moove
 * @copyright  2018 onwards Amanda Doughty (amanda.doughty.1@city.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');

$cid = required_param('cid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$returnurl = new moodle_url('/course/view.php', ['id' => $cid]);
$coursecontext = context_course::instance($cid);

// If we have got here as a confirmed action, do it.
if ($confirm && isloggedin() && confirm_sesskey()) {
    require_capability('moodle/course:update', $coursecontext);

    // Make the course visible.
    show_activitydates($cid);
    redirect($returnurl, get_string('activitydatesshown', 'theme_cul_moove'));
}

// Otherwise, show a confirmation page.
$params = ['cid' => $cid, 'sesskey' => sesskey(), 'confirm' => 1];
$actionurl = new moodle_url('/theme/cul_moove/showactivitydates.php', $params);

$PAGE->set_context($coursecontext);
$PAGE->set_url($actionurl);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('showactivitydates', 'theme_cul_moove'));
echo $OUTPUT->box_start('generalbox', 'notice');

echo html_writer::tag('p', get_string('confirmactivitydates', 'theme_cul_moove'));
echo $OUTPUT->single_button($actionurl, get_string('showactivitydates', 'theme_cul_moove'), 'post');
echo $OUTPUT->single_button($returnurl, get_string('cancel'), 'get');

echo $OUTPUT->box_end();
echo $OUTPUT->footer();

/**
 * Makes hidden course visible.
 *
 * @param int $cid course id 
 */
function show_activitydates($cid) {
    global $DB;

    $coursecontext = context_course::instance($cid);

    if (has_capability('moodle/course:update', $coursecontext)) { 
        return $DB->set_field('course', 'showactivitydates', 1, ['id' => $cid]);
    }

    return false;
}