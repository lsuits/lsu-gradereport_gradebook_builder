<?php

require_once '../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/report/gradebook_builder/lib.php';

$courseid = required_param('id', PARAM_INT);
$template_id = optional_param('template', null, PARAM_INT);

$c_param = array('id' => $courseid);

$PAGE->set_url(new moodle_url('/grade/report/gradebook_builder/index.php', $c_param));

$course = $DB->get_record('course', $c_param, '*', MUST_EXIST);

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);

require_capability('gradereport/gradebook_builder:view', $context);
require_capability('moodle/grade:viewall', $context);

$gpr = new grade_plugin_return(array(
    'type' => 'report',
    'plugin' => 'gradebook_builder',
    'courseid' => $courseid
));

if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}

$USER->grade_last_report[$course->id] = 'gradebook_builder';

$reportname = get_string('pluginname', 'gradereport_gradebook_builder');

print_grade_page_head($course->id, 'report', 'gradebook_builder', $reportname);

$report = new grade_report_gradebook_builder($courseid, $gpr, $context, $template_id);

$report->output();

echo $OUTPUT->footer();
