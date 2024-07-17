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
 * myoverview block renderer
 *
 * @package    block_myoverview
 * @copyright  2023 Onwards City University
 * @author Delvon Forrester <delvon.forrester@esparanza.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/blocks/myoverview/classes/output/main.php');

class theme_cul_moove_block_myoverview_renderer extends \block_myoverview\output\renderer {
    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_main($main) {
        $con = get_config('theme_cul_moove', 'monthtoswitchyearfilter');
        $month = $con ? $con : 'August';
        $year = date('Y');
        $lastyear = date('Y', strtotime("-1 year"));
        $strtime = strtotime("1-$month-$year");
        $newobject = $main->export_for_template($this);
        $newob = [];
        if (isset($newobject['customfieldvalues']) && is_array($newobject['customfieldvalues'])) {
            foreach ($newobject['customfieldvalues'] as $k => $ob) {
                $key = substr($ob->value, 0, 4);
                if ($ob->value == -1) {
                    unset($newobject['customfieldvalues'][$k]);
                    continue;
                }
                if ($key == $lastyear) {
                    if (time() < $strtime) {
                        $newob = $ob;
                        unset($newobject['customfieldvalues'][$k]);
                        break;
                    }
                }
                if ($key == $year) {
                    if (time() > $strtime) {
                        $newob = $ob;
                        unset($newobject['customfieldvalues'][$k]);
                        break;
                    }
                }
            }
        }
        if (!empty($newob)) {
            array_unshift($newobject['customfieldvalues'], $newob);
            usort($newobject['customfieldvalues'], function ($a, $b) {
                return strcmp($b->value, $a->value);
            });
        }
        return $this->render_from_template('block_myoverview/main', $newobject);
    }
}
