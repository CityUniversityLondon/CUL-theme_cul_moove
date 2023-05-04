<?php
// This file is part of Ranking block for Moodle - http://moodle.org/
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
 * Theme cul_moove block settings file
 *
 * @package    theme_cul_moove
 * @copyright 2022 City University - https://www.city.ac.uk/
 * @author Delvon Forrester delvon.forrester@esparanza.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// This is used for performance, we don't need to know about these settings on every page in Moodle, only when
// we are looking at the admin settings pages.
if ($ADMIN->fulltree) {

    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.
    $settings = new theme_boost_admin_settingspage_tabs('themesettingcul_moove', get_string('configtitle', 'theme_cul_moove'));

    /*
    * ----------------------
    * General settings tab
    * ----------------------
    */
    $page = new admin_settingpage('theme_cul_moove_general', get_string('generalsettings', 'theme_cul_moove'));

    // Logo file setting.
    $name = 'theme_cul_moove/cullogo';
    $title = get_string('logo', 'theme_cul_moove');
    $description = get_string('logodesc', 'theme_cul_moove');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'), 'maxfiles' => 1);
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo', 0, $opts);
    $page->add($setting);

    //Institution themes.
    $name = 'theme_cul_moove/institutions';
    $title = get_string('institutions', 'theme_cul_moove');
    $description = get_string('institutionsdesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    
    $inst = get_config('theme_cul_moove', 'institutions');
    if ($inst) {
        $inst_options = [];
        $institutions = explode(',', $inst);
        foreach($institutions as $key => $ins) {
            $file_ins = strtolower($ins);
            //if ($DB->record_exists('user', ['institution' => strtoupper($ins)])) {
                $setting = new admin_setting_configstoredfile('theme_cul_moove/'.$file_ins, "$file_ins logo", "Logo for the $file_ins institution", 
                        $file_ins, 0, $opts);
                $page->add($setting);
            //}
        }
    }

    // Favicon setting.
    $name = 'theme_cul_moove/favicon';
    $title = get_string('favicon', 'theme_cul_moove');
    $description = get_string('favicondesc', 'theme_cul_moove');
    $opts = array('accepted_types' => array('.ico'), 'maxfiles' => 1);
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon', 0, $opts);
    $page->add($setting);

    // Preset.
    $name = 'theme_cul_moove/preset';
    $title = get_string('preset', 'theme_cul_moove');
    $description = get_string('preset_desc', 'theme_cul_moove');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_cul_moove', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_cul_moove/presetfiles';
    $title = get_string('presetfiles', 'theme_cul_moove');
    $description = get_string('presetfiles_desc', 'theme_cul_moove');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 10, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Login page background image.
    $name = 'theme_cul_moove/loginbgimg';
    $title = get_string('loginbgimg', 'theme_cul_moove');
    $description = get_string('loginbgimg_desc', 'theme_cul_moove');
    $opts = array('accepted_types' => array('.png', '.jpg', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbgimg', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brand-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_cul_moove/brandcolor';
    $title = get_string('brandcolor', 'theme_cul_moove');
    $description = get_string('brandcolor_desc', 'theme_cul_moove');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f47ad');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $navbar-header-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_cul_moove/secondarymenucolor';
    $title = get_string('secondarymenucolor', 'theme_cul_moove');
    $description = get_string('secondarymenucolor_desc', 'theme_cul_moove');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f47ad');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $fontsarr = [
        'Roboto' => 'Roboto',
        'Poppins' => 'Poppins',
        'Montserrat' => 'Montserrat',
        'Open Sans' => 'Open Sans',
        'Lato' => 'Lato',
        'Raleway' => 'Raleway',
        'Inter' => 'Inter',
        'Nunito' => 'Nunito',
        'Encode Sans' => 'Encode Sans',
        'Work Sans' => 'Work Sans',
        'Oxygen' => 'Oxygen',
        'Manrope' => 'Manrope',
        'Sora' => 'Sora',
        'Epilogue' => 'Epilogue'
    ];

    $name = 'theme_cul_moove/fontsite';
    $title = get_string('fontsite', 'theme_cul_moove');
    $description = get_string('fontsite_desc', 'theme_cul_moove');
    $setting = new admin_setting_configselect($name, $title, $description, 'Roboto', $fontsarr);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_cul_moove/enablecourseindex';
    $title = get_string('enablecourseindex', 'theme_cul_moove');
    $description = get_string('enablecourseindex_desc', 'theme_cul_moove');
    $default = 1;
    $choices = array(0 => get_string('no'), 1 => get_string('yes'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    $name = 'theme_cul_moove/customfield';
    $title = get_string('customfield', 'theme_cul_moove');
    $description = get_string('customfield_desc', 'theme_cul_moove');
    $default = 'academicyear';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_cul_moove/includeaccyearsfrom';
    $title = get_string('includeaccyearsfrom', 'theme_cul_moove');
    $description = get_string('includeaccyearsfrom_desc', 'theme_cul_moove');
    $default = date('y') - 1;
    $choices = [];
    $year = date('y');
    for($i=20;$i<=$year;$i++) {
        $choices[$i] = $i;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    $name = 'theme_cul_moove/includeaccyearsto';
    $title = get_string('includeaccyearsto', 'theme_cul_moove');
    $description = get_string('includeaccyearsto_desc', 'theme_cul_moove');
    $default = date('y') + 2;
    $choices = [];
    $year = date('y');
    for($i=$year;$i<=$default;$i++) {
        $choices[$i] = $i;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Enable accessibility.
    $ac_con = get_config('theme_cul_moove', 'enableaccessibility');
    if (!$ac_con) {
        $DB->execute("update {user_preferences} set value ='0'");
        $DB->execute("delete from {user_preferences} where name = 'themecul_moovesettings_fonttype'");
    }
    $name = 'theme_cul_moove/enableaccessibility';
    $title = get_string('enableaccessibility', 'theme_cul_moove');
    $description = get_string('enableaccessibility_desc', 'theme_cul_moove');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    /*
    * ----------------------
    * Advanced settings tab
    * ----------------------
    */
    $page = new admin_settingpage('theme_cul_moove_advanced', get_string('advancedsettings', 'theme_cul_moove'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_cul_moove/scsspre',
        get_string('rawscsspre', 'theme_cul_moove'), get_string('rawscsspre_desc', 'theme_cul_moove'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_cul_moove/scss', get_string('rawscss', 'theme_cul_moove'),
        get_string('rawscss_desc', 'theme_cul_moove'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Google analytics block.
    $name = 'theme_cul_moove/googleanalytics';
    $title = get_string('googleanalytics', 'theme_cul_moove');
    $description = get_string('googleanalyticsdesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    /*
    * -----------------------
    * Frontpage settings tab
    * -----------------------
    */
    $page = new admin_settingpage('theme_cul_moove_frontpage', get_string('frontpagesettings', 'theme_cul_moove'));

    // Disable teachers from cards.
    $name = 'theme_cul_moove/disableteacherspic';
    $title = get_string('disableteacherspic', 'theme_cul_moove');
    $description = get_string('disableteacherspicdesc', 'theme_cul_moove');
    $default = 1;
    $choices = array(0 => get_string('no'), 1 => get_string('yes'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Slideshow.
    $name = 'theme_cul_moove/slidercount';
    $title = get_string('slidercount', 'theme_cul_moove');
    $description = get_string('slidercountdesc', 'theme_cul_moove');
    $default = 0;
    $options = array();
    for ($i = 0; $i < 13; $i++) {
        $options[$i] = $i;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // If we don't have an slide yet, default to the preset.
    $slidercount = get_config('theme_cul_moove', 'slidercount');

    if (!$slidercount) {
        $slidercount = $default;
    }

    if ($slidercount) {
        for ($sliderindex = 1; $sliderindex <= $slidercount; $sliderindex++) {
            $fileid = 'sliderimage' . $sliderindex;
            $name = 'theme_cul_moove/sliderimage' . $sliderindex;
            $title = get_string('sliderimage', 'theme_cul_moove');
            $description = get_string('sliderimagedesc', 'theme_cul_moove');
            $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'), 'maxfiles' => 1);
            $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid, 0, $opts);
            $page->add($setting);

            $name = 'theme_cul_moove/slidertitle' . $sliderindex;
            $title = get_string('slidertitle', 'theme_cul_moove');
            $description = get_string('slidertitledesc', 'theme_cul_moove');
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
            $page->add($setting);

            $name = 'theme_cul_moove/slidercap' . $sliderindex;
            $title = get_string('slidercaption', 'theme_cul_moove');
            $description = get_string('slidercaptiondesc', 'theme_cul_moove');
            $default = '';
            $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
            $page->add($setting);
        }
    }

    $setting = new admin_setting_heading('slidercountseparator', '', '<hr>');
    $page->add($setting);

    $name = 'theme_cul_moove/displaymarketingbox';
    $title = get_string('displaymarketingboxes', 'theme_cul_moove');
    $description = get_string('displaymarketingboxesdesc', 'theme_cul_moove');
    $default = 1;
    $choices = array(0 => get_string('no'), 1 => get_string('yes'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    $displaymarketingbox = get_config('theme_cul_moove', 'displaymarketingbox');

    if ($displaymarketingbox) {
        // Marketingheading.
        $name = 'theme_cul_moove/marketingheading';
        $title = get_string('marketingsectionheading', 'theme_cul_moove');
        $default = 'Awesome App Features';
        $setting = new admin_setting_configtext($name, $title, '', $default);
        $page->add($setting);

        // Marketingcontent.
        $name = 'theme_cul_moove/marketingcontent';
        $title = get_string('marketingsectioncontent', 'theme_cul_moove');
        $default = 'cul_moove is a Moodle template based on Boost with modern and creative design.';
        $setting = new admin_setting_confightmleditor($name, $title, '', $default);
        $page->add($setting);

        for ($i = 1; $i < 5; $i++) {
            $filearea = "marketing{$i}icon";
            $name = "theme_cul_moove/$filearea";
            $title = get_string('marketingicon', 'theme_cul_moove', $i . '');
            $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
            $setting = new admin_setting_configstoredfile($name, $title, '', $filearea, 0, $opts);
            $page->add($setting);

            $name = "theme_cul_moove/marketing{$i}heading";
            $title = get_string('marketingheading', 'theme_cul_moove', $i . '');
            $default = 'Lorem';
            $setting = new admin_setting_configtext($name, $title, '', $default);
            $page->add($setting);

            $name = "theme_cul_moove/marketing{$i}content";
            $title = get_string('marketingcontent', 'theme_cul_moove', $i . '');
            $default = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod.';
            $setting = new admin_setting_confightmleditor($name, $title, '', $default);
            $page->add($setting);
        }

        $setting = new admin_setting_heading('displaymarketingboxseparator', '', '<hr>');
        $page->add($setting);
    }

    // Enable or disable Numbers sections settings.
    $name = 'theme_cul_moove/numbersfrontpage';
    $title = get_string('numbersfrontpage', 'theme_cul_moove');
    $description = get_string('numbersfrontpagedesc', 'theme_cul_moove');
    $default = 1;
    $choices = array(0 => get_string('no'), 1 => get_string('yes'));
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    $numbersfrontpage = get_config('theme_cul_moove', 'numbersfrontpage');

    if ($numbersfrontpage) {
        $name = 'theme_cul_moove/numbersfrontpagecontent';
        $title = get_string('numbersfrontpagecontent', 'theme_cul_moove');
        $description = get_string('numbersfrontpagecontentdesc', 'theme_cul_moove');
        $default = get_string('numbersfrontpagecontentdefault', 'theme_cul_moove');
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $page->add($setting);
    }

    // Enable FAQ.
    $name = 'theme_cul_moove/faqcount';
    $title = get_string('faqcount', 'theme_cul_moove');
    $description = get_string('faqcountdesc', 'theme_cul_moove');
    $default = 0;
    $options = array();
    for ($i = 0; $i < 11; $i++) {
        $options[$i] = $i;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $page->add($setting);

    $faqcount = get_config('theme_cul_moove', 'faqcount');

    if ($faqcount > 0) {
        for ($i = 1; $i <= $faqcount; $i++) {
            $name = "theme_cul_moove/faqquestion{$i}";
            $title = get_string('faqquestion', 'theme_cul_moove', $i . '');
            $setting = new admin_setting_configtext($name, $title, '', '');
            $page->add($setting);

            $name = "theme_cul_moove/faqanswer{$i}";
            $title = get_string('faqanswer', 'theme_cul_moove', $i . '');
            $setting = new admin_setting_confightmleditor($name, $title, '', '');
            $page->add($setting);
        }

        $setting = new admin_setting_heading('faqseparator', '', '<hr>');
        $page->add($setting);
    }

    $settings->add($page);

    /*
    * --------------------
    * Footer settings tab
    * --------------------
    */
    $page = new admin_settingpage('theme_cul_moove_footer', get_string('footersettings', 'theme_cul_moove'));

    // Website.
    /*$name = 'theme_cul_moove/website';
    $title = get_string('website', 'theme_cul_moove');
    $description = get_string('websitedesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Mobile.
    $name = 'theme_cul_moove/mobile';
    $title = get_string('mobile', 'theme_cul_moove');
    $description = get_string('mobiledesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Mail.
    $name = 'theme_cul_moove/mail';
    $title = get_string('mail', 'theme_cul_moove');
    $description = get_string('maildesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Facebook url setting.
    $name = 'theme_cul_moove/facebook';
    $title = get_string('facebook', 'theme_cul_moove');
    $description = get_string('facebookdesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Twitter url setting.
    $name = 'theme_cul_moove/twitter';
    $title = get_string('twitter', 'theme_cul_moove');
    $description = get_string('twitterdesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Linkdin url setting.
    $name = 'theme_cul_moove/linkedin';
    $title = get_string('linkedin', 'theme_cul_moove');
    $description = get_string('linkedindesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Youtube url setting.
    $name = 'theme_cul_moove/youtube';
    $title = get_string('youtube', 'theme_cul_moove');
    $description = get_string('youtubedesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Instagram url setting.
    $name = 'theme_cul_moove/instagram';
    $title = get_string('instagram', 'theme_cul_moove');
    $description = get_string('instagramdesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Whatsapp url setting.
    $name = 'theme_cul_moove/whatsapp';
    $title = get_string('whatsapp', 'theme_cul_moove');
    $description = get_string('whatsappdesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);

    // Telegram url setting.
    $name = 'theme_cul_moove/telegram';
    $title = get_string('telegram', 'theme_cul_moove');
    $description = get_string('telegramdesc', 'theme_cul_moove');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $page->add($setting);*/

    $setting = new admin_setting_confightmleditor('theme_cul_moove/footer', 'Footer text', 'Text for the top of the footer', '');
   
    $page->add($setting);
       $setting = new admin_setting_confightmleditor('theme_cul_moove/footerbottom', 'Footer bottom text', 'Text for the bottom of the footer', '');
    $page->add($setting);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($page); 
}
