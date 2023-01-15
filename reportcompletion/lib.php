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
 *
 * @package    Reportcompletion
 * @copyright  2023 Mohaned Hassan (mohaned.omran1@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_reportcompletion_extend_settings_navigation( $settingsnav, $context) {
    global $CFG, $PAGE, $ADMIN;
    // Only let users with the appropriate capability see this settings item.
    if (!has_capability('moodle/backup:backupcourse', context_course::instance($PAGE->course->id))) {
        return;
    }
    if (is_siteadmin() && $settingnode = $settingsnav->find('root', navigation_node::TYPE_SITE_ADMIN)) {
        $strname = get_string('reportcompletion', 'local_reportcompletion');
        $url = new moodle_url('/local/reportcompletion/index.php');
        $reportnode = navigation_node::create($strname, $url,
        navigation_node::NODETYPE_LEAF, 'local_reportcompletion',
        'local_reportcompletion',
        new pix_icon('i/report', $strname));
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $reportnode->make_active();
        }
        $ADMIN->add('reports', new admin_externalpage('local_reportcompletion', $strname, $url));

        $settingnode->add_node($reportnode);
    }
}
