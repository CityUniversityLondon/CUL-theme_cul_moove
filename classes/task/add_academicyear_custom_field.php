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
 * Schedule tasks for Adding academic year custom field to courses
 *
 * @package     theme
 * @subpackage  cul_moove
 * @copyright   2023 Onwards City University
 * @author      Delvon Forrester <delvon.forrester@esparanza.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cul_moove\task;

/**
 * A scheduled task to add academic year custom field to courses.
 * /usr/bin/php admin/tool/task/cli/schedule_task.php --execute=\\theme_cul_moove\\task\\add_academicyear_custom_field
 */
class add_academicyear_custom_field extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('academicyeartask', 'theme_cul_moove');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;
        $yearfrom = get_config('theme_cul_moove', 'includeaccyearsfrom') ?: date('y') - 1;
        $yearto = get_config('theme_cul_moove', 'includeaccyearsto') ?: date('y') + 1;
        $customfieldconfig = get_config('theme_cul_moove', 'customfield') ?: 'academicyear';
        $customfield = $DB->get_field('customfield_field', 'id', ['shortname' => $customfieldconfig]);
        $courses = $DB->get_recordset('course', [], $sort = '', 'id,shortname');
        foreach ($courses as $k => $course) {
            $shortname = substr(trim($course->shortname), -7);
            $year = substr($shortname, -2);
            $pattern = "/(\d{4})-(\d{2})/";
            if (preg_match($pattern, $shortname) && $year >= $yearfrom && $year <= $yearto) {
                $customdata = $DB->get_record('customfield_data', ['fieldid' => $customfield, 'instanceid' => $k]);
                if ($customdata) {
                    $customdata->charvalue = $shortname;
                    $customdata->value = $shortname;
                    $customdata->timemodified = time();
                    $DB->update_record('customfield_data', $customdata);
                } else {
                    $newrecord = new \stdClass();
                    $newrecord->fieldid = $customfield;
                    $newrecord->instanceid = $k;
                    $newrecord->charvalue = $shortname;
                    $newrecord->value = $shortname;
                    $newrecord->valueformat = 0;
                    $newrecord->timecreated = time();
                    $newrecord->timemodified = time();
                    $newrecord->contextid = context_course::instance($k)->id;
                    $DB->insert_record('customfield_data', $newrecord);
                }
            }
        }
        $courses->close();
    }

}
