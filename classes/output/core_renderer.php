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
 * Overriden theme boost core renderer.
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

        $fonttype = get_user_preferences('themecul_moovesettings_fonttype', '');
        if ($fonttype) {
            $additionalclasses[] = $fonttype;
        }

        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }

        return ' id="'. $this->body_id().'" class="'.$this->body_css_classes($additionalclasses).'"';
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
        if ($inst && isset($theme->settings->{$inst})) {
            return $theme->setting_file_url($inst, $inst);
        }
        return $theme->setting_file_url('logo', 'logo');
    }
    
    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        /*global $CFG;
        $logo = get_config('core_admin', 'logocompact');
        if (empty($logo)) {
            return false;
        }

        // Hide the requested size in the file path.
        $filepath = ((int) $maxwidth . 'x' . (int) $maxheight) . '/';

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(context_system::instance()->id, 'core_admin', 'logocompact', $filepath,
            theme_get_revision(), $logo);*/
        global $DB, $USER;
        $inst = strtolower($DB->get_field('user', 'institution', ['id' => $USER->id]));
        $theme = theme_config::load('cul_moove');
        if ($inst && isset($theme->settings->{$inst})) {
            return $theme->setting_file_url($inst, $inst);
        }
        return $theme->setting_file_url('logo', 'logo');
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
        $context->sitename = format_string($SITE->fullname, true,
            ['context' => context_course::instance(SITEID), "escape" => false]);

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
        $content = $icon . $label;

        if (!empty($CFG->supportpage)) {
            $attributes = ['href' => $CFG->supportpage, 'target' => 'blank', 'class' => 'btn contactsitesupport btn-outline-info'];
        } else {
            $attributes = [
                'href' => $CFG->wwwroot . '/user/contactsitesupport.php',
                'class' => 'btn contactsitesupport btn-outline-info'
            ];
        }

        $attributes += $customattribs;

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
        //if (isset($contextheader->imagedata)) {
            // Header specific image.
            $img = html_writer::link(new moodle_url('/'),
                html_writer::empty_tag('img', array(
                'src' => $this->get_theme_logo_url(),
                'alt' => get_string('home'))),
                array('title' => get_string('home')));
            $html .= html_writer::div($img, 'page-header-image mr-2');
        //}

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
     * Returns standard navigation between activities in a course.
     *
     * @return string the navigation HTML.
     */
    public function activity_navigation() {
        // First we should check if we want to add navigation.
        $context = $this->page->context;
        if (($this->page->pagelayout !== 'incourse' && $this->page->pagelayout !== 'frametop')
            || $context->contextlevel != CONTEXT_MODULE) {
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

        // This function is normally called from a layout.php file
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in the footer.
        $output .= $this->unique_performance_info_token;

        if (!empty($CFG->debugpageinfo)) {
            $output .= '<div class="performanceinfo pageinfo">' . get_string('pageinfodebugsummary', 'core_admin',
                $this->page->debug_summary()) . '</div>';
        }
        if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', \context_system::instance())) {  // Only in developer mode

            // Add link to profiling report if necessary
            if (function_exists('profiling_is_running') && profiling_is_running()) {
                $txt = get_string('profiledscript', 'admin');
                $title = get_string('profiledscriptview', 'admin');
                $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
                $link= '<a title="' . $title . '" href="' . $url . '">' . $txt . '</a>';
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

        $theme->settings->googleanalytics;
        return $output.'
                
           <div class="footerlinks d-flex flex-wrap text-center text-md-left py-5">
            <div class="footnote col-12 col-md-6 mb-4 mb-md-0 p-0">
                <p></p><p></p>
                <p>Cass Business School has been renamed to Bayes Business School. There may still be references to Cass Business School in content produced before September 2021.&nbsp;<span style="font-size: 1rem; letter-spacing: 0.1px;">Find out more about the name change <a href="https://www.bayes.city.ac.uk/about/more/our-name-change" target="_blank">on our website</a>.</span></p><p>City, University of London is an independent member institution of 
                    the University of London. Established by Royal Charter in 1836, the 
                    University of London consists of 18 independent member institutions with
                    outstanding global reputations and several
                    prestigious central academic bodies and activities.</p>
                <p></p>
                <h1 class="h5">Contact us</h1>+44 (0)20 7040 5060
                <br><a href="https://www.city.ac.uk/about/find-contact/contact" target="_blank">Make an enquiry
                </a><br>
                <p></p><br><p></p>
            </div>
            <img class="footerlogo mx-auto mr-md-0 ml-md-auto"
                 src="https://moodle.city.ac.uk/theme/image.php/cul_boost/theme/1664260351/city-footerlogo"
                 alt="City, University of London"/>
        </div></div>
        <div class="footer-bottom py-3">
            <div class="container-fluid d-flex flex-wrap align-items-between">
                <div>&copy; 2022 City, University of London</div>
                <a href="#topofscroll" class="scrolltop text-white mx-auto">Back to top
                    <i class="fa fa-angle-up ml-2"></i>
                </a>
                <div>
                    <p></p>
                    <p><a href="https://www.city.ac.uk/about/city-information/legal" target="_blank">Legal</a></p>
                    <p><a href="https://cityuni.service-now.com/">Help &amp; Support</a></p>
                    <p><a href="https://blogs.city.ac.uk/accessibility/moodle/" id="accessibility">Accessibility</a></p>
                    <p></p>
                </div>
            </div>
        </div><style>page-context-header img {width:100px;max-height: 80px;}
footer#page-footer .bg-dark{ background-color: #a22833!important;}
footer#page-footer .footer-bottom.py-3 {background-color: darkred;}
#page-footer .copyright {background-color: darkred;}
#page.drawers .main-inner {margin-top: 1.5rem;}
@media (min-width: 1000px) {
.page-context-header {margin-left: -80px;}
}
.pagelayout-standard #page.drawers .footer-popover, body.limitedwidth #page.drawers .footer-popover {
    width: 100%;max-width:100%}
#page.drawers {
    padding: 0;}</style>';
    }

}
