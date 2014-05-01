<?php

require_once '../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/report/gradebook_builder/lib.php';

$courseid = required_param('id', PARAM_INT);
$template_id = optional_param('template', null, PARAM_INT);

$c_param = array('id' => $courseid);

$PAGE->set_url(new moodle_url('/grade/report/gradebook_builder/index.php', $c_param));

$PAGE->requires->css('/grade/report/gradebook_builder/bootstrap.css');
$PAGE->requires->css('/grade/report/gradebook_builder/app.css');

$course = $DB->get_record('course', $c_param, '*', MUST_EXIST);

require_login($course);

$context = context_course::instance($course->id);

require_capability('gradereport/gradebook_builder:view', $context);
require_capability('moodle/grade:edit', $context);

$gpr = new grade_plugin_return(array(
    'type' => 'report',
    'plugin' => 'gradebook_builder',
    'courseid' => $courseid
));

$reportname = get_string('pluginname', 'gradereport_gradebook_builder');

$template = $DB->get_record('gradereport_builder_template', array(
    'id' => $template_id
));

$report = new grade_report_gradebook_builder($courseid, $gpr, $context, $template);

if ($data = data_submitted() and !$report::is_gradebook_established($courseid)) {
    $report->process_data($data);
}

$report->inject_js();

print_grade_page_head($course->id, 'report', 'gradebook_builder', $reportname);

if ($report::is_gradebook_established($courseid)) {
    $gradebook_url = new moodle_url('/grade/edit/tree/index.php', array(
        'id' => $course->id
    ));
    echo $OUTPUT->notification(get_string('items', 'gradereport_gradebook_builder'));
    echo $OUTPUT->continue_button($gradebook_url);
} else {
    $report->output();
}

echo $OUTPUT->footer();
