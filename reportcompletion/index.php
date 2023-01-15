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
 * Run the code checker from the web.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir . '/formslib.php');
require_login();

global $PAGE, $DB, $OUTPUT;
$page        = optional_param('page', '0', PARAM_INT);     // Which page to show.
$id          = optional_param('id', 0, PARAM_INT);// Course ID.


    $context = context_system::instance();
    $PAGE->set_context($context);


if (!is_siteadmin()) {
    throw new moodle_exception();
}
$params = array();
if ($page !== '0') {
    $params['page'] = $page;
}
$userid = optional_param('userid', 0, PARAM_INT);
$url = new moodle_url("/local/reportcompletion/index.php", $params);

$PAGE->set_url($url);
$output = $PAGE->get_renderer('report_log');
echo $output->header();
if ($userid) {
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
    $courses = enrol_get_all_users_courses($userid);

    echo '<h2>'.fullname($user).'</h2>';
    $table = new html_table();
    $table->head = array(get_string('course'),
    get_string('completionstatus', 'local_reportcompletion'),
    get_string('timecompleted', 'local_reportcompletion'));
    $table->attributes = array('class' => 'table table-bordered');
    $table->data = array();
    foreach ($courses as $course) {
        $completion = $DB->get_record('course_completions', array('userid' => $userid, 'course' => $course->id));
        $row = array();
        $row[] = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), $course->fullname);
        $row[] = ($completion->timecompleted) ? get_string('complete',
        'local_reportcompletion') : get_string('notcomplete',
        'local_reportcompletion');
        $row[] = userdate($completion->timecompleted, get_string('datetimeformat',
        'local_reportcompletion'));
        $table->data[] = $row;
    }
    echo html_writer::table($table);
} else {
    $users = $DB->get_records('user');
    echo '<h2>'.get_string('selectuser', 'local_reportcompletion').'</h2>';
    echo '<form method="post" action="index.php">';
    echo '<div class="form-group">';
    echo '<label for="userid">'.get_string('selectuser', 'local_reportcompletion').'</label>';
    echo '<select class="form-control" name="userid" id="userid">';
    foreach ($users as $user) {
        echo '<option value="'.$user->id.'">'.fullname($user).'</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '<input class="btn btn-primary" type="submit" value="'.get_string('viewreport', 'local_reportcompletion').'">';
    echo '</form>';
}
echo $OUTPUT->footer();
