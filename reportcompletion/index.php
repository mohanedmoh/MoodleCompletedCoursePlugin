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

require_login();

global $PAGE, $DB, $OUTPUT;
$page        = optional_param('page', '0', PARAM_INT);     // Which page to show.
$id          = optional_param('id', 0, PARAM_INT);// Course ID.


if (!is_siteadmin()) {
    print_error('accessdenied', 'admin');
}
$params = array();
if ($page !== '0') {
    $params['page'] = $page;
}
$userid = optional_param('userid', 0, PARAM_INT);
$url = new moodle_url("/local/reportcompletion/index.php", $params);

$PAGE->set_url($url);
if ($userid) {
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
    $courses = enrol_get_all_users_courses($userid);

    echo '<h2>'.fullname($user).'</h2>';
    echo '<div class="datatable">';
    echo '<table >';
    echo '<tr><th>'.get_string('course').'</th><th>'.get_string('completionstatus','local_reportcompletion').'</th><th>'.get_string('timecompleted','local_reportcompletion').'</th></tr>';

    foreach ($courses as $course) {
        $completion = $DB->get_record('course_completions', array('userid' => $userid, 'course' => $course->id));
        echo '<tr>';
        echo '<td><a href="'.new moodle_url('/course/view.php', array('id' => $course->id)).'">'.$course->fullname.'</a></td>';
        echo '<td>'.(($completion->timecompleted) ? get_string('complete','local_reportcompletion') : get_string('notcomplete','local_reportcompletion')).'</td>';
        echo '<td>'.userdate($completion->timecompleted, get_string('datetimeformat','local_reportcompletion')).'</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '</div>';
} else {

    $users = $DB->get_records('user');
    echo '<h2>'.get_string('selectuser','local_reportcompletion').'</h2>';
    echo '<form>';
    echo '<select name="userid">';
    foreach ($users as $user) {
        echo '<option value="'.$user->id.'">'.fullname($user).'</option>';
    }
    echo '</select>';
    echo '<input type="submit" value="'.get_string('viewreport','local_reportcompletion').'">';
    echo '</form>';
}
