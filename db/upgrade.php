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
 * Upgrade script for theme_cul_moove
 *
 * @package     theme_cul_moove
 * @author      Delvon Forrester <delvon.forrester@esparanza.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_theme_cul_moove_upgrade($oldversion) {
    if ($oldversion < 2023032200) {
        // Delete SCOMCR to try and fix stuck setting.
        unset_config('scomcr', 'theme_cul_moove');
        unset_config('SCOMCR', 'theme_cul_moove');
        purge_all_caches();

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2023032200, 'theme', 'cul_moove');
    }    
    return true;
}
