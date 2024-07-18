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
 * A drawer based layout for the cul_moove theme.
 *
 * @package    theme_cul_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG, $DB, $USER, $OUTPUT, $SITE, $PAGE;

// Get the profile userid.
$courseid = optional_param('course', 1, PARAM_INT);
$userid = optional_param('id', $USER->id, PARAM_INT);
$userid = $userid ? $userid : $USER->id;
$user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

function updateuserazureb2c($userid, $idtoken) {
    global $CFG, $OUTPUT, $USER, $PAGE, $DB;
    require_once("{$CFG->dirroot}/auth/azureb2c/classes/loginflow/base.php");
    $idtoken = \auth_azureb2c\jwt::instance_from_encoded($idtoken);
    $username = $idtoken->claim('oid');
    if (!empty($username)) {
        $firstname = $idtoken->claim('given_name');
        $lastname = $idtoken->claim('family_name');
        $country = $idtoken->claim('country');
        $countryval = "";
        if (!empty($country)) {
            $countries = get_string_manager()->get_list_of_countries();
            foreach ($countries as $countrykey => $countryvalue) {
                $countryb2c = $country;
                $countrymoodle = $countryvalue;
                if ($countrymoodle == $countryb2c) {
                    $countryval = $countrykey;
                }
            }
        }
        $gender = $idtoken->claim('extension_WP_Gender');

        $userupdate = new \stdClass();
        $userupdate->id = $userid;
        if (!empty($firstname)) {
            $userupdate->firstname = $firstname;
        }
        if (!empty($lastname)) {
            $userupdate->lastname = $lastname;
        }
        if (!empty($countryval)) {
            $userupdate->country = $countryval;
        }
        $DB->update_record('user', $userupdate);

        $USER->firstname = $firstname;
        $USER->lastname = $lastname;
        $USER->country = $countryval;
        return true;
    }
}

$idtoken = "";
if (!empty($_COOKIE['id_token'])) {
    $idtoken = $_COOKIE['id_token'];
    $idtoken = explode("=", $idtoken);
}

$editcheck = get_user_preferences('auth_azureb2c_edit');
if ($editcheck == 0 || $editcheck == 2) {
    if ($editcheck == 0) {
        set_user_preference('auth_azureb2c_edit', 2);
        header("Refresh:0");
    }
    if ($editcheck == 2) {
        $userid = optional_param('id', null, PARAM_INT);
        if (!empty($idtoken[1]) && ($idtoken[0] == "#id_token")) {
            updateuserazureb2c($userid, $idtoken[1]);
            $_COOKIE['id_token'] = null;
            set_user_preference('auth_azureb2c_edit', 1);
        }
    }
}

$azureurl = get_config('auth_azureb2c', 'editprofileendpoint') . "&client_id=" . get_config('auth_azureb2c', 'clientid') . "&
	nonce=defaultNonce&redirect_uri=" . $CFG->wwwroot . "/auth/azureb2c/&scope=openid&response_type=id_token";

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$bodyattributes = $OUTPUT->body_attributes([]);

$userimg = new \user_picture($user);
$userimg->size = 100;

$context = context_course::instance(SITEID);

$usercanviewprofile = user_can_view_profile($user);


$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'primarymoremenu' => $primarymenu['moremenu'],
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'userpicture' => $userimg->get_url($PAGE),
    'userfullname' => fullname($user),
    'headerbuttons' => \theme_cul_moove\util\extras::get_mypublic_headerbuttons($context, $user),
    'editprofileurl' => \theme_cul_moove\util\extras::get_mypublic_editprofile_url($user, $courseid),
    'azureurl' => $azureurl,
    'usercanviewprofile' => $usercanviewprofile,
];

if ($usercanviewprofile) {
    $countries = get_string_manager()->get_list_of_countries(true);

    $templatecontext['userdescription'] = format_text($user->description, $user->descriptionformat, ['overflowdiv' => true]);
    $templatecontext['usercountry'] = $user->country ? $countries[$user->country] : '';
    $templatecontext['usercity'] = $user->city;

    if ($userid == $USER->id) {
        $templatecontext['useremail'] = $user->email;
    } else {
        if (!empty($courseid)) {
            $canviewuseremail = has_capability('moodle/course:useremail', $context);
        } else {
            $canviewuseremail = false;
        }

        $showuseridentityfields = \core_user\fields::get_identity_fields($context, false);

        if (($user->maildisplay == core_user::MAILDISPLAY_EVERYONE
            || ($user->maildisplay == core_user::MAILDISPLAY_COURSE_MEMBERS_ONLY && enrol_sharing_course($user, $USER))
            || $canviewuseremail  // TODO: Deprecate/remove for MDL-37479.
            )
            || in_array('email', $showuseridentityfields)) {
            $templatecontext['useremail'] = $user->email;
        }
    }
}

$themesettings = new \theme_cul_moove\util\settings();

$templatecontext = array_merge($templatecontext, $themesettings->footer());

echo $OUTPUT->render_from_template('theme_cul_moove/mypublic', $templatecontext);
