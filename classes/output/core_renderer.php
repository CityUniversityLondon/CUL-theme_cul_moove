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
 * Overridden theme boost core renderer.
 *
 * @package    theme_cul_moove
 * @copyright 2022 City University - https://www.city.ac.uk/
 * @author Delvon Forrester delvon.forrester@esparanza.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_cul_moove\output;

use theme_config;
use context_course;
use moodle_url;
use html_writer;
use theme_cul_moove\output\core_course\activity_navigation;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_cul_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * The standard tags (meta tags, links to stylesheets and JavaScript, etc.)
     * that should be included in the <head> tag. Designed to be called in theme
     * layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_head_html() {
        $output = parent::standard_head_html();

        $googleanalyticscode = "<script
                                    async
                                    src='https://www.googletagmanager.com/gtag/js?id=GOOGLE-ANALYTICS-CODE'>
                                </script>
                                <script>
                                    window.dataLayer = window.dataLayer || [];
                                    function gtag() {
                                        dataLayer.push(arguments);
                                    }
                                    gtag('js', new Date());
                                    gtag('config', 'GOOGLE-ANALYTICS-CODE');
                                </script>";

        $theme = theme_config::load('cul_moove');

        if (!empty($theme->settings->googleanalytics)) {
            $output .= str_replace("GOOGLE-ANALYTICS-CODE", trim($theme->settings->googleanalytics), $googleanalyticscode);
        }

        $sitefont = isset($theme->settings->fontsite) ? $theme->settings->fontsite : 'Roboto';

        $output .= '<link rel="preconnect" href="https://fonts.googleapis.com">
                       <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                       <link href="https://fonts.googleapis.com/css2?family='
                . $sitefont .
                ':ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">';

        return $output;
    }

    /**
     * Returns HTML attributes to use within the body tag. This includes an ID and classes.
     *
     * @param string|array $additionalclasses Any additional classes to give the body tag,
     *
     * @return string
     *
     * @throws \coding_exception
     *
     * @since Moodle 2.5.1 2.6
     */
    public function body_attributes($additionalclasses = array()) {
        $hasaccessibilitybar = get_user_preferences('themecul_moovesettings_enableaccessibilitytoolbar', '');
        if ($hasaccessibilitybar) {
            $additionalclasses[] = 'hasaccessibilitybar';

            $currentfontsizeclass = get_user_preferences('accessibilitystyles_fontsizeclass', '');
            if ($currentfontsizeclass) {
                $additionalclasses[] = $currentfontsizeclass;
            }

            $currentsitecolorclass = get_user_preferences('accessibilitystyles_sitecolorclass', '');
            if ($currentsitecolorclass) {
                $additionalclasses[] = $currentsitecolorclass;
            }
        }

        if (has_capability('mod/assign:submit', context_course::instance($this->page->course->id), null, false)) {
            $additionalclasses[] = 'student';
        }

        $fonttype = get_user_preferences('themecul_moovesettings_fonttype', '');
        if ($fonttype) {
            $additionalclasses[] = $fonttype;
        }

        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }

        // Add student if user is a student on any course.
        global $USER, $COURSE, $DB;
        if (!get_config('theme_cul_moove', 'showrollovertool')) {
            if (!$this->get_user_capability_in_any_course('moodle/course:update', $USER->id)) {
                $additionalclasses[] = 'rollover-student';
            }
        }

        // Check if culcourse_dashboard block is on course page.
        // If so remove the side-pre region when configuring block.
        $ctx = context_course::instance($COURSE->id)->id;
        if ($dash = $DB->get_record('block_instances', ['blockname' =>
            'dashboard', 'parentcontextid' => $ctx])) {
            $additionalclasses[] = 'culblock';
            if ($dash->defaultregion == 'side-pre') {
                $additionalclasses[] = 'cul-side-pre';
            }
        } else {
            $additionalclasses[] = 'noculblock';
        }

        return ' id="' . $this->body_id() . '" class="' . $this->body_css_classes($additionalclasses) . '"';
    }

    /**
     * Function that checks if users is a student in any course.
     *
     * @return bool
     */
    private function get_user_capability_in_any_course($capability, $userid) {
    global $DB;

    return $DB->get_records_sql("select 1 from {role_assignments} where userid = ? and roleid in
            (select roleid from {role_capabilities} where capability = ?) limit 1", [$userid, $capability]);
    }

    /**
     * Whether we should display the main theme or site logo in the navbar.
     *
     * @return bool
     */
    public function should_display_logo() {
        if ($this->should_display_theme_logo() || parent::should_display_navbar_logo()) {
            return true;
        }

        return false;
    }

    /**
     * Whether we should display the main theme logo in the navbar.
     *
     * @return bool
     */
    public function should_display_theme_logo() {
        $logo = $this->get_theme_logo_url();

        return !empty($logo);
    }

    /**
     * Get the main logo URL.
     *
     * @return string
     */
    public function get_logo() {
        $logo = $this->get_theme_logo_url();

        if ($logo) {
            return $logo;
        }

        $logo = $this->get_logo_url();

        if ($logo) {
            return $logo->out(false);
        }

        return false;
    }

    /**
     * Get the main logo URL.
     *
     * @return string
     */
    public function get_theme_logo_url() {
        global $DB, $USER;
        $inst = strtolower($DB->get_field('user', 'institution', ['id' => $USER->id]));
        $theme = theme_config::load('cul_moove');
        if ($inst && isset($theme->settings->$inst) && $theme->settings->$inst) {
            return $theme->setting_file_url($inst, $inst);
        }
        return $theme->setting_file_url('cullogo', 'logo');
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        global $DB, $USER;
        $inst = strtolower($DB->get_field('user', 'institution', ['id' => $USER->id]));
        $theme = theme_config::load('cul_moove');
        if ($inst && isset($theme->settings->$inst) && $theme->settings->$inst) {
            return $theme->setting_file_url($inst, $inst);
        }
        return $theme->setting_file_url('cullogo', 'logo');
    }

    /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $SITE, $CFG;

        $context = $form->export_for_template($this);

        $context->errorformatted = $this->error_text($context->error);
        $context->logourl = $this->get_logo();
        $context->sitename = format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]);

        if (!$CFG->auth_instructions) {
            $context->instructions = null;
            $context->hasinstructions = false;
        }

        $context->hastwocolumns = false;
        if ($context->hasidentityproviders || $CFG->auth_instructions) {
            $context->hastwocolumns = true;
        }

        if ($context->identityproviders) {
            foreach ($context->identityproviders as $key => $provider) {
                $isfacebook = false;

                if (strpos($provider['iconurl'], 'facebook') !== false) {
                    $isfacebook = true;
                }

                $context->identityproviders[$key]['isfacebook'] = $isfacebook;
            }
        }

        return $this->render_from_template('core/loginform', $context);
    }

    /**
     * Returns the HTML for the site support email link
     *
     * @param array $customattribs Array of custom attributes for the support email anchor tag.
     * @return string The html code for the support email link.
     */
    public function supportemail(array $customattribs = []): string {
        global $CFG;

        $label = get_string('contactsitesupport', 'admin');
        $icon = $this->pix_icon('t/life-ring', '', 'moodle', ['class' => 'iconhelp icon-pre']);
        $newwindowicon = $this->pix_icon('i/externallink', get_string('opensinnewwindow'), 'moodle', ['class' => 'ml-1']);
        $content = $icon . $label . $newwindowicon;

        if (!empty($CFG->supportpage)) {
            $attributes = ['href' => $CFG->supportpage, 'target' => 'blank', 'class' => 'contactsitesupport'];
        } else {
            $attributes = [
                'href' => $CFG->wwwroot . '/user/contactsitesupport.php',
                'class' => 'contactsitesupport'
            ];
        }

        return \html_writer::tag('a', $content, $attributes);
    }

    /**
     * Returns the moodle_url for the favicon.
     *
     * @since Moodle 2.5.1 2.6
     * @return moodle_url The moodle_url for the favicon
     */
    public function favicon() {
        global $CFG;

        $theme = theme_config::load('cul_moove');

        $favicon = $theme->setting_file_url('favicon', 'favicon');

        if (!empty(($favicon))) {
            $urlreplace = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
            $favicon = str_replace($urlreplace, '', $favicon);

            return new moodle_url($favicon);
        }

        return parent::favicon();
    }

    /**
     * Renders the header bar.
     *
     * @param \context_header $contextheader Header bar object.
     * @return string HTML for the header bar.
     */
    protected function render_context_header(\context_header $contextheader) {
        if ($this->page->pagelayout == 'mypublic') {
            return '';
        }

        // Generate the heading first and before everything else as we might have to do an early return.
        if (!isset($contextheader->heading)) {
            $heading = $this->heading($this->page->heading, $contextheader->headinglevel, 'h2');
        } else {
            $heading = $this->heading($contextheader->heading, $contextheader->headinglevel, 'h2');
        }

        // All the html stuff goes here.
        $html = html_writer::start_div('page-context-header');

        // Image data.
        if (isset($contextheader->imagedata)) {
            // Header specific image.
            $html .= html_writer::div($contextheader->imagedata, 'page-header-image mr-2');
        }

        // Headings.
        if (isset($contextheader->prefix)) {
            $prefix = html_writer::div($contextheader->prefix, 'text-muted text-uppercase small line-height-3');
            $heading = $prefix . $heading;
        }
        $html .= html_writer::tag('div', $heading, array('class' => 'page-header-headings'));

        // Buttons.
        if (isset($contextheader->additionalbuttons)) {
            $html .= html_writer::start_div('btn-group header-button-group');
            foreach ($contextheader->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'togglecontact') {
                        \core_message\helper::togglecontact_requirejs();
                    }
                    if ($button['buttontype'] === 'message') {
                        \core_message\helper::messageuser_requirejs();
                    }
                    $image = $this->pix_icon($button['formattedimage'], $button['title'], 'moodle', array(
                        'class' => 'iconsmall',
                        'role' => 'presentation'
                    ));
                    $image .= html_writer::span($button['title'], 'header-button-title');
                } else {
                    $image = html_writer::empty_tag('img', array(
                                'src' => $button['formattedimage'],
                                'role' => 'presentation'
                    ));
                }
                $html .= html_writer::link($button['url'], html_writer::tag('span', $image), $button['linkattributes']);
            }
            $html .= html_writer::end_div();
        }
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
     public function full_header() {
        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();
        $homepagetype = null;
        // Add a special case since /my/courses is a part of the /my subsystem.
        if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
            $homepagetype = 'my-index';
        } else if ($homepage == HOMEPAGE_SITE) {
            $homepagetype = 'site-index';
        }
        if ($this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                            $this->region_main_settings_menu(), 'd-print-none', ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new \stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = str_replace('Preferences:', '', $this->context_header());
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->culincourse = isset($this->page->cm->id) ? 1 : 0;
        $header->coursemainpage = $pagetype == 'course-view-culcourse' ? 1 : 0;
        //$header->culicon = isset($this->page->cm->id) ? html_writer::img($this->page->cm->get_icon_url()->out(false), '', ['class' => 'icon activityicon', 'aria-hidden' => 'true']) : '';
        $wikiheader = false;
        if (in_array($pagetype, ['mod-ouwiki-view'])) {
            $wikiheader = true;
        }
        $header->culcontextheader = $wikiheader ? str_replace('Preferences:', '',
                $this->heading($this->page->course->fullname, 2, 'h2')) :
                str_replace('Preferences:', '', $this->heading($this->page->heading, 2, 'h2'));
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            $header->welcomemessage = \core_user::welcome_message();
        }
        $header->gradebook_disclaimer = $this->gradebook_disclaimer();
        return $this->render_from_template('core/full_header', $header);
    }

    /**
     * Returns standard navigation between activities in a course.
     *
     * @return string the navigation HTML.
     */
    public function activity_navigation() {
        // First we should check if we want to add navigation.
        $context = $this->page->context;
        if (($this->page->pagelayout !== 'incourse' && $this->page->pagelayout !== 'frametop') || $context->contextlevel != CONTEXT_MODULE) {
            return '';
        }

        // If the activity is in stealth mode, show no links.
        if ($this->page->cm->is_stealth()) {
            return '';
        }

        $course = $this->page->cm->get_course();
        $courseformat = course_get_format($course);

        // Get a list of all the activities in the course.
        $modules = get_fast_modinfo($course->id)->get_cms();

        // Put the modules into an array in order by the position they are shown in the course.
        $mods = [];
        $activitylist = [];
        foreach ($modules as $module) {
            // Only add activities the user can access, aren't in stealth mode and have a url (eg. mod_label does not).
            if (!$module->uservisible || $module->is_stealth() || empty($module->url)) {
                continue;
            }
            $mods[$module->id] = $module;

            // No need to add the current module to the list for the activity dropdown menu.
            if ($module->id == $this->page->cm->id) {
                continue;
            }
            // Module name.
            $modname = $module->get_formatted_name();
            // Display the hidden text if necessary.
            if (!$module->visible) {
                $modname .= ' ' . get_string('hiddenwithbrackets');
            }
            // Module URL.
            $linkurl = new moodle_url($module->url, array('forceview' => 1));
            // Add module URL (as key) and name (as value) to the activity list array.
            $activitylist[$linkurl->out(false)] = $modname;
        }

        $nummods = count($mods);

        // If there is only one mod then do nothing.
        if ($nummods == 1) {
            return '';
        }

        // Get an array of just the course module ids used to get the cmid value based on their position in the course.
        $modids = array_keys($mods);

        // Get the position in the array of the course module we are viewing.
        $position = array_search($this->page->cm->id, $modids);

        $prevmod = null;
        $nextmod = null;

        // Check if we have a previous mod to show.
        if ($position > 0) {
            $prevmod = $mods[$modids[$position - 1]];
        }

        // Check if we have a next mod to show.
        if ($position < ($nummods - 1)) {
            $nextmod = $mods[$modids[$position + 1]];
        }

        $activitynav = new activity_navigation($prevmod, $nextmod, $activitylist);
        $renderer = $this->page->get_renderer('core', 'course');
        return $renderer->render($activitynav);
    }

    /**
     * Returns plugins callback renderable data to be printed on navbar.
     *
     * @return string Final html code.
     */
    public function get_navbar_callbacks_data() {
        $callbacks = get_plugins_with_function('cul_moove_additional_header', 'lib.php');

        if (!$callbacks) {
            return '';
        }

        $output = '';

        foreach ($callbacks as $plugins) {
            foreach ($plugins as $pluginfunction) {
                if (function_exists($pluginfunction)) {
                    $output .= $pluginfunction();
                }
            }
        }

        return $output;
    }

    /**
     * Performance information and validation links for debugging.
     *
     * @return string HTML fragment.
     */
    public function debug_footer_html() {
        global $CFG, $SCRIPT;
        $output = '';

        if (during_initial_install()) {
            // Debugging info can not work before install is finished.
            return $output;
        }

        $output .= $this->unique_performance_info_token;

        if (!empty($CFG->debugpageinfo)) {
            $output .= '<div class="performanceinfo pageinfo">' . get_string('pageinfodebugsummary', 'core_admin', $this->page->debug_summary()) . '</div>';
        }
        if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', \context_system::instance())) {  // Only in developer mode
            // Add link to profiling report if necessary
            if (function_exists('profiling_is_running') && profiling_is_running()) {
                $txt = get_string('profiledscript', 'admin');
                $title = get_string('profiledscriptview', 'admin');
                $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
                $link = '<a title="' . $title . '" href="' . $url . '">' . $txt . '</a>';
                $output .= '<div class="profilingfooter">' . $link . '</div>';
            }
            $purgeurl = new moodle_url('/admin/purgecaches.php', array('confirm' => 1,
                'sesskey' => sesskey(), 'returnurl' => $this->page->url->out_as_local_url(false)));
            $output .= '<div class="purgecaches">' .
                    html_writer::link($purgeurl, get_string('purgecaches', 'admin')) . '</div>';

            // Reactive module debug panel.
            $output .= $this->render_from_template('core/local/reactive/debugpanel', []);
        }
        if (!empty($CFG->debugvalidators)) {
            $siteurl = qualified_me();
            $nuurl = new moodle_url('https://validator.w3.org/nu/', ['doc' => $siteurl, 'showsource' => 'yes']);
            $waveurl = new moodle_url('https://wave.webaim.org/report#/' . urlencode($siteurl));
            $validatorlinks = [
                html_writer::link($nuurl, get_string('validatehtml')),
                html_writer::link($waveurl, get_string('wcagcheck'))
            ];
            $validatorlinkslist = html_writer::alist($validatorlinks, ['class' => 'list-unstyled ml-1']);
            $output .= html_writer::div($validatorlinkslist, 'validators');
        }
        $theme = theme_config::load('cul_moove');

        $output .= $theme->settings->footer . '</div>' . $theme->settings->footerbottom;
        return $output;
    }

    /**
     * Returns a button to make a hidden course visible.
     *
     * @return string the HTML to be output.
     */
    public function show_course_button() {

        global $COURSE, $OUTPUT;

        $content = '';
        $coursecontext = context_course::instance($COURSE->id);

        if (!has_capability('moodle/course:update', $coursecontext)) {
            return $content;
        }

        $showcourseurl = new moodle_url(
            '/theme/cul_moove/unhide_post.php', [
            'cid' => $COURSE->id,
            'sesskey' => sesskey()
                ]
        );

        $showcoursetxt = get_string('showcourse', 'theme_cul_moove');

        return $OUTPUT->single_button($showcourseurl, $showcoursetxt, 'post', ['class' => 'showcourse d-inline-block ml-4']);
    }

    /**
     * Returns a button to make a hidden course visible.
     *
     * @return string the HTML to be output.
     */
    public function show_activitydates_button() {

        global $COURSE, $OUTPUT;

        $content = '';
        $coursecontext = context_course::instance($COURSE->id);

        if (!has_capability('moodle/course:update', $coursecontext)) {
            return $content;
        }

        $showcourseurl = new moodle_url(
            '/theme/cul_moove/showactivitydates.php', [
            'cid' => $COURSE->id,
            'sesskey' => sesskey()
                ]
        );

        $showactivitydatestxt = get_string('showactivitydates', 'theme_cul_moove');

        return $OUTPUT->single_button($showcourseurl, $showactivitydatestxt, 'post', ['class' => 'showcourse d-inline-block ml-4']);
    }

    /**
     * Checks if page requires gradebook discalimer.
     *
     * @return bool true if page requires discalimer.
     */
    public function gradebook_disclaimer() {
        $gradebookids = array(
            'page-grade-report-user-index',
            'page-grade-report-culuser-index',
            'page-grade-report-overview-index',
            //'page-course-user'
        );

        if (in_array($this->page->bodyid, $gradebookids)) {
            return true;
        }

        return false;
    }

    public function is_accessibility_enabled() {
        return get_config('theme_cul_moove', 'enableaccessibility');
    }

}
