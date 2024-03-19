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
 * Theme functions.
 *
 * @package    theme_cul_moove
 * @copyright 2022 City University - https://www.city.ac.uk/
 * @author Delvon Forrester delvon.forrester@esparanza.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_cul_moove_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_cul_moove', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // cul_moove scss.
    $cul_moovevariables = file_get_contents($CFG->dirroot . '/theme/cul_moove/scss/cul_moove/_variables.scss');
    $cul_moove = file_get_contents($CFG->dirroot . '/theme/cul_moove/scss/default.scss');

    // Combine them together.
    $allscss = $cul_moovevariables . "\n" . $scss . "\n" . $cul_moove;

    return $allscss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_cul_moove_get_extra_scss($theme) {
    $content = '';
    $originscss = '/* MDL4-266 City Styles */
textarea#id_s_theme_cul_moove_scss { height: 600px; }
a { transition: all 0.2s ease-in-out !important; }
p {
    margin-top: 5px !important;
    padding-left: 5px !important;
}
a.focus { background-color: #00ff00; }

/* link styles */
div.event-name-container .event-name a:link:not(.btn),
div.activitytitle div.activityname a:link:not(.btn),
div.course-description-item div.description-inner a:link:not(.btn),
div.filemanager .fp-filename-icon a:link:not(.btn),
li.modtype_label a:link:not(.btn),
li.modtype_bootstrapelements a:link:not(.btn),
body.path-mod-page div#page div#page-content div#region-main-box div.box a:link:not(.btn),
body.path-mod-forum div#page div#page-content div#region-main-box div.body-content-container a:link:not(.btn),
body.path-mod-data div#page div#page-content div#region-main-box div.data-field-html a:link:not(.btn),
body.path-mod-book div#page div#page-content div#region-main-box div.book_content a:link:not(.btn),
body.path-mod-glossary div#page div#page-content div#region-main-box td.entry a:link:not(.btn),
body.path-mod-feedback div#page div#page-content div#region-main-box div.felement a:link:not(.btn),
div.activity-header div.activity-description a:link:not(.btn),
div.policy_document a:link:not(.btn),
div.editor_atto_content_wrap a:link:not(.btn),
div.modal-content div.modal-body p a:link:not(.btn),
div.modal-content div.modal-body li a:link:not(.btn),
body.path-admin-tool-policy div#page div#page-content div#region-main-box a:link:not(.btn) {
    color: #006bbd;
    text-decoration: none !important;
    border-bottom: 0.2rem solid #006bbd;
}
/* link visited styles */
div.event-name-container .event-name a:visited:not(.btn),
div.activitytitle div.activityname a:visited:not(.btn),
div.course-description-item div.description-inner a:visited:not(.btn),
div.filemanager .fp-filename-icon a:visited:not(.btn),
li.modtype_label a:visited:not(.btn),
li.modtype_bootstrapelements a:visited:not(.btn),
body.path-mod-page div#page div#page-content div#region-main-box div.box a:visited:not(.btn),
body.path-mod-forum div#page div#page-content div#region-main-box div.body-content-container a:visited:not(.btn),
body.path-mod-data div#page div#page-content div#region-main-box div.data-field-html a:visited:not(.btn),
body.path-mod-book div#page div#page-content div#region-main-box div.book_content a:visited:not(.btn),
body.path-mod-glossary div#page div#page-content div#region-main-box td.entry a:visited:not(.btn),
body.path-mod-feedback div#page div#page-content div#region-main-box div.felement a:visited:not(.btn),
div.activity-header div.activity-description a:visited:not(.btn),
div.policy_document a:visited:not(.btn),
div.editor_atto_content_wrap a:visited:not(.btn),
div.modal-content div.modal-body p a:visited:not(.btn),
div.modal-content div.modal-body li a:visited:not(.btn),
body.path-admin-tool-policy div#page div#page-content div#region-main-box a:visited:not(.btn) {
    color: #4c2c92;
    background-color: #fff;
    text-decoration: none;
    border-bottom: 0.2rem solid #4c2c92;
}
/* link hover styles */
div.event-name-container .event-name a:hover:not(.btn),
div.activitytitle div.activityname a:hover:not(.btn),
div.course-description-item div.description-inner a:hover:not(.btn),
div.filemanager .fp-filename-icon a:hover:not(.btn),
li.modtype_label a:hover:not(.btn),
li.modtype_bootstrapelements a:hover:not(.btn),
body.path-mod-page div#page div#page-content div#region-main-box div.box a:hover:not(.btn),
body.path-mod-forum div#page div#page-content div#region-main-box div.body-content-container a:hover:not(.btn),
body.path-mod-data div#page div#page-content div#region-main-box div.data-field-html a:hover:not(.btn),
body.path-mod-book div#page div#page-content div#region-main-box div.book_content a:hover:not(.btn),
body.path-mod-glossary div#page div#page-content div#region-main-box td.entry a:hover:not(.btn),
body.path-mod-feedback div#page div#page-content div#region-main-box div.felement a:hover:not(.btn),
div.activity-header div.activity-description a:hover:not(.btn),
div.policy_document a:hover:not(.btn),
div.editor_atto_content_wrap a:hover:not(.btn),
div.modal-content div.modal-body p a:hover:not(.btn),
div.modal-content div.modal-body li a:hover:not(.btn),
body.path-admin-tool-policy div#page div#page-content div#region-main-box a:hover:not(.btn) {
    color: #fff;
    background-color: #006bbd;
    text-decoration: none;
    border-bottom: 0.2rem solid #006bbd;
}
/* link active styles */
div.event-name-container .event-name a:active:not(.btn),
div.activitytitle div.activityname a:active:not(.btn),
div.course-description-item div.description-inner a:active:not(.btn),
div.filemanager .fp-filename-icon a:active:not(.btn),
li.modtype_label a:active:not(.btn),
li.modtype_bootstrapelements a:active:not(.btn),
body.path-mod-page div#page div#page-content div#region-main-box div.box a:active:not(.btn),
body.path-mod-forum div#page div#page-content div#region-main-box div.body-content-container a:active:not(.btn),
body.path-mod-data div#page div#page-content div#region-main-box div.data-field-html a:active:not(.btn),
body.path-mod-book div#page div#page-content div#region-main-box div.book_content a:active:not(.btn),
body.path-mod-glossary div#page div#page-content div#region-main-box td.entry a:active:not(.btn),
body.path-mod-feedback div#page div#page-content div#region-main-box div.felement a:active:not(.btn),
div.activity-header div.activity-description a:active:not(.btn),
div.policy_document a:active:not(.btn),
div.editor_atto_content_wrap a:active:not(.btn),
div.modal-content div.modal-body p a:active:not(.btn),
div.modal-content div.modal-body li a:active:not(.btn),
body.path-admin-tool-policy div#page div#page-content div#region-main-box a:active:not(.btn) {
    color: #fff;
    background-color: #006bbd;
    text-decoration: none;
    outline-color: #0c0c0e;
    outline-offset: 0.15rem;
    outline-style: solid;
    outline-width: 0.125rem;
    border: none;
    box-shadow: none;
}
/* link focus styles */
div.event-name-container .event-name a:focus:not(.btn),
div.activitytitle div.activityname a:focus:not(.btn),
div.course-description-item div.description-inner a:focus:not(.btn),
div.filemanager .fp-filename-icon a:focus:not(.btn),
li.modtype_label a:focus:not(.btn),
li.modtype_bootstrapelements a:focus:not(.btn),
body.path-mod-page div#page div#page-content div#region-main-box div.box a:focus:not(.btn),
body.path-mod-forum div#page div#page-content div#region-main-box div.body-content-container a:focus:not(.btn),
body.path-mod-data div#page div#page-content div#region-main-box div.data-field-html a:focus:not(.btn),
body.path-mod-book div#page div#page-content div#region-main-box div.book_content a:focus:not(.btn),
body.path-mod-glossary div#page div#page-content div#region-main-box td.entry a:focus:not(.btn),
body.path-mod-feedback div#page div#page-content div#region-main-box div.felement a:focus:not(.btn),
div.activity-header div.activity-description a:focus:not(.btn),
div.policy_document a:focus:not(.btn),
div.editor_atto_content_wrap a:focus:not(.btn),
div.modal-content div.modal-body p a:focus:not(.btn),
div.modal-content div.modal-body li a:focus:not(.btn),
body.path-admin-tool-policy div#page div#page-content div#region-main-box a:focus:not(.btn) {
    color: #fff;
    background-color: #006bbd;
    text-decoration: none;
    outline-color: #0c0c0e;
    outline-offset: 0.15rem;
    outline-style: solid;
    outline-width: 0.125rem;
    border: none;
    box-shadow: none;
}
.fp-filename {
    padding-right: 0px !important;
}
span.ally-actions {
    padding: 0 10px;
}

/* login page */
form.login-form {
    display: none !important;
}
body#page-local-cullogin-index div.container-fluid {
    max-width: 100% !important;
}

/* footer styling */
footer#page-footer .bg-dark {
    background-color: #0c0c0c !important;
}
footer#page-footer .footer-bottom.py-3 {
    background-color: darkred;
}
#page-footer .copyright {
    background-color: darkred;
}
div.cityfooter {
    background-color: #0c0c0c !important;
}
div.cityfooter-col-left {
    float: left;
    width: 75%;
    padding: 10px;
    vertical-align: top;
    font-size: .9rem;
    text-align: left;
}
div.cityfooter-col-right{
    float: right;
    width: 25%;
    padding: 10px;
    vertical-align: top;
    text-align: center;
}
img.cityfooter-logo {
    max-width: 200px;
    height: auto;
}
div.cityfooter:after {
    content:"";
    display: table;
    clear: both;
}
.pagelayout-standard #page.drawers .footer-popover, body.limitedwidth #page.drawers .footer-popover {
    width: 100%;
    max-width:100%
}
#local_culcourse_dashboard .container-fluid, .cul_moove-container-fluid.footer-columns{
    max-width: 100%;
}
#page-footer {
    background-color: #333!important;
}
#page.drawers {
    padding: 0!important;
}
/* activity icon colours */
.activityiconcontainer, .activityiconcontainer.collaboration, .activityiconcontainer.assessment, li.activity.modtype_hsuforum .activityiconcontainer.other,
.activityiconcontainer.content, .activityiconcontainer.communication, .activityiconcontainer.administration, .modchoosercontainer div[data-internal="hsuforum"] .modicon_hsuforum {
    background: #fff;
}
.activityiconcontainer.collaboration .activityicon, .activityiconcontainer.collaboration .icon, li.activity.modtype_hsuforum .activityiconcontainer img.activityicon,
.activityiconcontainer.content .activityicon, .activityiconcontainer.content .icon, .modchoosercontainer div[data-internal="hsuforum"] .modicon_hsuforum img.activityicon,
.activityiconcontainer.assessment .activityicon, .activityiconcontainer.assessment .icon,
.activityiconcontainer.communication .activityicon, .activityiconcontainer.communication .icon,
.activityiconcontainer.administration .activityicon, .activityiconcontainer.administration .icon
{
    filter: none!important;
}
/* temporary until the dashboard class is sorted on City 41 */
#page-course-view-culcourse .course-content > .container-fluid {
    max-width:100%;
}
/* end of temp */

body#page-course-view-culcourse.editing .viewmorelink {
    display: none !important;
}
body {
    font-size: 1.075rem;
}
/*  print css */
@media print {
    h2.module-hidden, footer#page-footer .cul_moove-container-fluid.footer-dark-inner,
    footer#page-footer .purgecaches, #page-navbar .breadcrumb, #accessibilitybar, .card-link,
    .filter-group, .initialbar, .enrolusersbutton, .buttons, div#local_culcourse_dashboard,
    button#local_culcourse_dashboard_toggledashboard, div.activity-navigation,
    #page-course-view-participants ul[data-region="photoboard-tabs"], #page-course-view-participants h2,
    #page-course-view-participants p[data-region="participant-count"], .btn-footer-popover,
    #moveTop, .pagination, a[data-action="showcount"], footer#page-footer, .footer-bottom {
        display:none !important;
    }
    @page {
        margin: 20px;
    }
    #page-course-view-participants .card {
        border: none;
    }
}

#page-course-view-grid .justify-content-between {
    justify-content: flex-start !important;
}
#page-course-view-participants .userlist .card,
#page-course-view-participants .card-deck.briefuser .card {
    display: table;
}
@media (min-width: 1100px) {
    #page-course-view-participants .userlist .card,
    #page-course-view-participants .card-deck.briefuser .card {
        width: calc(33.33% - 2rem);
    }
}
@media (min-width: 840px) {
    #page-course-view-participants .userlist .card,
    #page-course-view-participants .card-deck.briefuser .card {
        width: calc(50% - 2rem);
    }
}
@media (min-width: 576px) {
    #page-course-view-participants .userlist .card,
    #page-course-view-participants .card-deck.briefuser .card {
        width: calc(100%);
    }
}
@media (min-width: 576px) {
    #page-course-view-participants .userlist .card,
    #page-course-view-participants .card-deck .card {
        flex: 1 0 15%;
        margin-right: 0.25rem;
        margin-bottom: 0;
        margin-left: 0.25rem;
    }
}

.course-content ul.culcourse li.section {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

/* Mikes section tweaking CSS */
footer#page-footer .footer-columns {
    padding: 0;
}
footer#page-footer  {
    margin: 0;
}
footer#page-footer .cul_moove-container-fluid .footer-dark-inner {
    padding-top: 20px;
    padding-bottom: 20px;
}

/* Mike fix for KalVidRes description showing twice */
body#page-mod-kalvidres-view .activity-header {
    display: none;
}
body#page-mod-kalvidassign-view .activity-header {
    display: none;
}
.activity-add, .block-add {
    color: #0f6cbf;
    background-color: #f5f9fc;
    border: 1px solid #3584c9;
    padding: 8px!important;
}
.block-add {
    padding: 4px 4px 4px 8px!important;
}
.activity-add:hover, .block-add:hover {
    color: #0f6cbf;
    background-color: #d8e7f3;
    border: 1px solid #3584c9;
}
.activity-add .pluscontainer {
    border: none;
    padding: 0px!important;
}
/* styling dashboard photoboard cards */
li.card {
    text-align: center;
}
.card-link {
    font-size: 0.8em;
}
/* styling for debugging message */
.debuggingmessage {
    margin-top: 200px!important;
    margin-left: 90px;
    margin-right: 100px;
    margin-bottom: 0px!important;
    background-color: pink;
    padding: 15px 15px 1px 15px;
}
/* Mike for dashboard icons we have overridden
.dash-icon .icon {
    font-size: inherit;
    color: #000;
}*/
/* Mike for folder icon size --- make bigger */
span.fp-icon img.icon {
    width: 24px;
    height: 24px;
}
/* Mike justify breadcrumb */
.breadcrumb {
    justify-content: flex-start;
}
/* Delvon changes for grade alert-warning */
#page-grade-report-culuser-index .alert-warning {
    margin-bottom: 0px;
}
/* MDL4-228 activity description font size too small */
small, .small {
    font-size: 90%!important;
}
/* MDL4-222 question chooser font size too small */
.choosercontainer #chooseform .option {
    font-size: 90%;
}
/* MDL4-207 message drawer  */
body.hasaccessibilitybar .drawer .message-app {
    height: 100%;
    margin-top: 31px;
}
/* MDL4-136 Sticky footer */
.stickyfooter {
    padding-right: 80px!important;
}
/* Hide the Home tab */
.primary-navigation li[data-key="home"] {
  display: none;
}
/* MDL4-94 Fix photoboard in participants list*/
#page-course-view-participants ul.userlist {
    display: flex;
    flex-flow: row wrap;
}
/* MDL4-123 grid section nav */
#page-course-view-grid .section-navigation.mdl-bottom > div:nth-child(2) {
    margin: auto;
}
#page-course-view-grid .section-navigation.navigationtitle {
    display: none!important;
}
/* styling grid completion button */
 .grid-completion.grid-completion-colour-low {
    background-color: #e2e2e6!important;
    color: black!important;
}
.format-grid .grid-completion {
    border-width: 2px!important;
    border-color: #d61726!important;
}
/* MDL4-131 grid styling */
.gridsectionbreakheading {
    margin-top: 40px!important;
}
.card {
    border: 1px solid #e2e2e6;
}
/*MDL4 -20*/
.courseindex-item-content .text-truncate {
    white-space: normal;
}
/*MDL4-109 */
#page-my-index .card-footer .dropdown-menu.show {
    transform: translate3d(-180px, -50px, 0px) !important;
}
/* MDL4-206 styling for back to top button */
button#moveTop {
  display: none;
  position: fixed;
  bottom: 65px;
  right: 32px;
}
/* make sure it stays inline with support  button when block drawer is open */
@media (min-width: 992px) {
    .jsenabled #page.drawers.show-drawer-right button#moveTop {
        right: calc(315px + 2rem);
    }
}
/* MDL4-223 styling for expand/collapse button */
#page-course-view-culcourse .collapseall.text-nowrap, #page-course-view-culcourse .expandall.text-nowrap {
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    user-select: none;
    color: #d61726;
    background-color: #f1f4f4;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 0.95rem;
    line-height: 1.5;
    border-radius: 0.5rem;
}
#page-course-view-culcourse .collapseall.text-nowrap:hover, #page-course-view-culcourse .expandall.text-nowrap:hover {
    background-color: #c4cdd4;
    border-color: #b1bbc4;
    color: #8e232d;
}
a#collapsesections {
     text-decoration: none;
}
/*MDL4-71*/
@media (max-width: 576px) {
    .section-navigation.mdl-bottom.d-flex.justify-content-between {
        display: block !important;
   }
}
/*MDL4-165*/
@media (max-width: 768px) {
    div.datapreferences > form > div.d-flex > div {
        max-width: 82%;
    }
/*MDL4-77 do not allow bloc drawer at tablet portrait and below*/
        /*@media (min-width: 400px) {
         .dash-link-inner .dash-text  {
            margin-left: -8px !important;
            font-size: 14px !important;
        }
    }*/
    /*MDL4-229*/
    .drawer-toggles .drawer-toggler {
        top: 40% !important;
    }
}
@media (max-width: 400px) {
    div.datapreferences > form > div.d-flex > div {
        max-width: 98%;
    }
}
/*MDL4-240*/
@media (max-width: 767.98px) {
    .card .card-body {
        max-width: 105px;
        min-width: 105px;}
}
/*MDL4-238*/
#page-contentbank .word-break-all {
    word-break: break-word;
}
/*MDL4-191*/
.path-mod-assign [data-region="grading-navigation"] {
    padding: 5px;
}
/* MDL4-219 restrict text width to 80 characters */
div#page-content li.activity p {
    max-width: 80ch;
}
div#page-content div.box.py-3.generalbox {
    max-width: 80ch;
}
div#page-content div.course-description-item.summarytext {
    max-width: 80ch;
}
div#page-content div.course-section-header {
    max-width: 80ch;
}
div#page-content div.availabilityinfo {
    max-width: 80ch;
}
div#page-content div.activity-dates.course-description-item.small {
    max-width: 80ch;
}
div#page-content div.activity-altcontent{
    max-width: 80ch;
}
div#page-content div.activity-description {
    max-width: 80ch;
}
div#page-content div.policy_document {
    max-width: 80ch;
}
/* rollover table */
div#existing-records table.dataTable {
    width: 100%!important;
}
/* remove 200px padding in editing mode */
.editing .section .activity .contentwithoutlink, .editing .section .activity .activityinstance {
    padding-right: 10px!important;
}
/* MDL4-50 City styles atto */
.citystyles.block {
    line-height: 1.5;
    margin: 0 0.25rem 0.25rem 0;
    padding: 0.25rem 0.4em;
    font-size: 1.125rem;
    font-weight: 700;
    white-space: normal;
    display: block;
    border-radius: 0.25rem;
    width: auto;
}
.cul-important {
    background-color: #c30000;
    color: #fff;
}
.cul-todo {
    background-color: #717176;
    color: #fff;
}
.citystyles.inline {
    line-height: 1;
    padding: 0.25rem 0.4em;
    font-size: 75%;
    font-weight: 700;
    white-space: normal;
    display: inline;
    border-radius: 10rem;
    text-align: center;
}
.cul-success {
    background-color: #64a44e;
    color: #fff;
}
.cul-info {
    background-color: #008196;
    color: #fff;
}
.cul-warning {
    background-color: #f0ad4e;
    color: #1d2125;
}
.cul-danger {
    background-color: #ca3120;
    color: #fff;
}
/* accessibility */
.text-muted {
    /*color: #666f75!important;*/
    /*font-size: 80%!important;*/
}
/* MDL4-266 City branding and colors */
.secondary-navigation .navigation .nav-tabs {
    justify-content: center;
}
body .local_culcourse_dashboard.dash-panel.quicklinks-wrap {
    background-color: #b91924;
}
body .local_culcourse_dashboard.dash-panel.activities-wrap {
    background-color: #d61726;
}
.secondary-navigation .moremenu .nav-tabs .nav-link:hover {
    background-color: #9e111c;
}
.secondary-navigation .moremenu .nav-tabs .nav-link:focus {
    background-color: #9e111c;
    border: 2px solid #fff;
    border-radius: 0.3rem;
}
.dash-link a {
    color: #1d2125 !important;
}
.dash-link a.disabled {
  opacity: 0.5;
}
.dash-text.nolink.text-muted {
    font-style: normal !important;
    color: #1d2125 !important;
}
button#local_culcourse_dashboard_toggledashboard.btn.btn-primary {
    background-color: #f1f4f4;
    color: #d61726;
    border: 1px solid transparent;
    border-radius: 0.3rem;
}
button#local_culcourse_dashboard_toggledashboard.btn.btn-primary.collapsed {
    background-color: #b92124;
    color: #fff;
    border: 1px solid transparent;
}
button#local_culcourse_dashboard_toggledashboard:hover {
    background-color: #c4cdd4 !important;
    color: #8e232d!important;
    border: 1px solid #b1bbc4 !important;
}
.dash-link.linkhidden {
    background-color: #ccc !important;
}
.dash-link a.align-items-center {
    align-items: start !important;
    margin-top: 5px;
}
span.ally-download  a.ally-prominent-af-download-button {
    text-decoration: none !important;
    border: none !important;
}
.ally-download a:hover, .ally-download a:focus{
    background-color: #fff !important;
}
a.quickeditlink {
    text-decoration: none !important;
    border: none !important;
}
div.dropdown-menu a:link.dropdown-item {
    text-decoration: none !important;
    border-bottom: none !important;
    color: #1d2125 !important;
}
div.dropdown-menu a:hover.dropdown-item {
    text-decoration: none !important;
    border-bottom: none !important;
    color: #fff !important;
}
.dropdown-item:active, .dropdown-item:hover, .dropdown-item:focus, .dropdown-item:focus-within {
    background-color: #b91924 !important;
}
/*MDL4-109*/
#page-my-index .block .block-cards .dashboard-card-footer.menu {
    opacity: 1;
}
/*MDL4-7*/
#page-mod-assign-grader .alert-dismissible {
    padding-right: 2rem;
}
/*MDL4-287*/
@media (max-width: 767.98px) {
    .card .card-body {
        max-width: 100%;
    }
}
/*MDL4-77, MDL4-272*/
body .local_culcourse_dashboard.dash-panel .linkscontainer ul.links .dash {
    min-width: 135px;
}
/*MDL4-242*/
.path-mod-forum #region-main > div > div > div.no-overflow {
    overflow:unset;
}
/*MDL4-279*/
.path-course-view li.activity.label > div {
        padding: 1rem !important;
}
/*MDL4-268*/
body#page-grade-report-grader-index {
    background-color: unset !important;
}
/*#page-grade-report-grader-index #page-wrapper #page {
    width: fit-content;
}
#page-grade-report-grader-index #page.drawers .main-inner {
    max-width: 100%;
}
#page-grade-report-grader-index #page-header {
    margin: 1.25rem 2.5rem;
}*/
/*MDL4-356*/
.nav-link {
    padding: 0.5rem 0.8rem;
}
@media (max-width: 510px) {
.secondary-navigation .moremenu {
    height: 62px;
}
  /*  .nav-link {
        padding: 0.5rem 0.2rem;
    }
body {font-size: 0.9rem;}*/
}
/*MDL4-264*/
#page-block-quickmail.course-1 .secondary-navigation.cul_moove {
  display: none;
}
/*MDL4-375*/
.editor_atto_content.form-control, .dash-link a {
    overflow-wrap: anywhere;
    overflow-wrap: break-word;
    word-wrap: break-word;
}
#page-admin-tool-usertours-configure #fgroup_id_contenthtmlgrp fieldset.w-100 > div.d-flex.flex-wrap.align-items-center {
    display: contents !important;
}
/*MDL4-401*/
.rollover-student a[href*="culrollover"] {
    display:none;
}
/* page width override for JAD JS Bootcamp */
body#page-mod-page-view.course-6081 div.box.py-3.generalbox {
    max-width: 110ch !important;
}
/* page width override for Mike */
body#page-blocks-configurable_reports-editreport .CodeMirror {
    height: 800px;
    font-size: 80%;
}
/* h5p fixes for better display of images in slider and collapsible sections */
div.h5p-iframe-wrapper iframe.h5p-iframe {
  min-width: 100% !important;
}
div.h5p-image-hotspot-popup {
  width: 85% !important;
  background-color: red !important!
}
div.h5p-placeholder {
  max-width: 110ch !important;
}
/*Fix for the book chapters and culcourse_block icons*/
body .drawer#theme_boost-drawers-blocks:focus-within {
    position: fixed;
}
/*Fix for dashboard block image*/
#block-region-side-pre #local_culcourse_dashboard li.dash img {
    height: 16px;
    margin-right: 10px;
}';

    // Sets the login background image.
    $loginbgimgurl = $theme->setting_file_url('loginbgimg', 'loginbgimg');
    if (!empty($loginbgimgurl)) {
        $content .= 'body.pagelayout-login #page { ';
        $content .= "background-image: url('$loginbgimgurl'); background-size: cover;";
        $content .= ' }';
    }

    // Always return the background image with the scss when we have it.
    return !empty($theme->settings->scss) ? $originscss . ' ' . $theme->settings->scss
            . ' ' . $content : $originscss . ' ' . $content;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_cul_moove_get_pre_scss($theme) {
    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['brand-primary'],
        'secondarymenucolor' => 'secondary-menu-color',
        'fontsite' => 'font-family-sans-serif'
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            if ($target == 'fontsite') {
                $scss .= '$' . $target . ': "' . $value . '", sans-serif !default' .";\n";
            } else {
                $scss .= '$' . $target . ': ' . $value . ";\n";
            }
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_cul_moove_get_precompiled_css() {
    global $CFG;

    return file_get_contents($CFG->dirroot . '/theme/cul_moove/style/moodle.css');
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return mixed
 */
function theme_cul_moove_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $theme = theme_config::load('cul_moove');
    $inst = $theme->institutions;
    if ($inst) {
        $institutions = explode(',', $inst);
    } else {
        $institutions = [];
    }
    if ($context->contextlevel == CONTEXT_SYSTEM &&
        ($filearea === 'logo' || $filearea === 'loginbgimg' || $filearea == 'favicon' || in_array($filearea, $institutions))) {
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && preg_match("/^sliderimage[1-9][0-9]?$/", $filearea) !== false) {
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing1icon') {
        return $theme->setting_file_serve('marketing1icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing2icon') {
        return $theme->setting_file_serve('marketing2icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing3icon') {
        return $theme->setting_file_serve('marketing3icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing4icon') {
        return $theme->setting_file_serve('marketing4icon', $args, $forcedownload, $options);
    }

    send_file_not_found();
}

/**
 * Serves the grading panel as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 * @return string
 */
function theme_cul_moove_output_fragment_gradealert($args) {
    global $CFG, $OUTPUT;

    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot . '/mod/assign/locallib.php');

    $o = '';    
    $courseid = clean_param($args['courseid'], PARAM_INT); 
    $assignid = clean_param($args['assignid'], PARAM_INT); 
    $userid = clean_param($args['userid'], PARAM_INT); 
    $context = $args['context'];
    $cangrade = has_capability('mod/assign:grade', $context);

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }

    if($cangrade) {
        $gradinginfo = grade_get_grades(
            $courseid,
            'mod',
            'assign',
            $assignid,
            $userid
        );

        $gradingitem = null;
        $gradebookgrade = null;

        if (isset($gradinginfo->items[0])) {
            $gradingitem = $gradinginfo->items[0];
            $gradebookgrade = $gradingitem->grades[$userid];
        }

        if ($gradebookgrade->hidden){
            $o .= $OUTPUT->notification(get_string('gradehidden', 'theme_cul_moove'), 'error hazard');
        } else {
            $o .= $OUTPUT->notification(get_string('gradenothidden', 'theme_cul_moove'), 'error hazard');
        }
    }

    return $o;
}