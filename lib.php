<?php

require_once $CFG->dirroot . '/grade/report/lib.php';
require_once $CFG->libdir . '/quick_template/lib.php';

class grade_report_gradebook_builder extends grade_report {
    function process_data($data) {
    }

    function process_action($target, $action) {
    }

    function __construct($courseid, $gpr, $context, $template = null) {
        parent::__construct($courseid, $gpr, $context);

        if (!$template) {
            $template = new stdClass;
            $template->data = '{}';
        }

        $this->template = $template;
    }

    function inject_js() {
        global $PAGE;

        $module = array(
            'name' => 'gradereport_gradebook_builder',
            'fullpath' => '/grade/report/gradebook_builder/js/module.js',
            'requires' => array('base', 'dom', 'json')
        );

        $args = array('template_data' => $this->template->data);

        $PAGE->requires->js_init_call(
            'M.gradereport_gradebook_builder.init', $args, false, $module
        );
    }

    function output() {
        $data = array(
            'templates' => $this->get_templates()
        );

        quick_template::render('index.tpl', $data);
    }

    function get_templates() {
        global $USER, $DB;

        $levels = array(
            CONTEXT_SYSTEM => SITEID,
            CONTEXT_USER => $USER->id,
            CONTEXT_COURSECAT => $this->course->category,
            CONTEXT_COURSE => $this->course->id
        );

        $categoryname = $DB->get_field('course_categories', 'name', array(
            'id' => $this->course->category
        ));

        $labels = array(
            CONTEXT_SYSTEM => get_string('coresystem'),
            CONTEXT_USER => fullname($USER),
            CONTEXT_COURSECAT => $categoryname,
            CONTEXT_COURSE => $this->course->fullname
        );

        $options = array();
        // Gather templates at respective context levels
        foreach ($levels as $contextlevel => $instanceid) {
            $params = array(
                'contextlevel' => $contextlevel,
                'instanceid' => $instanceid
            );

            $templates = $DB->get_records_menu(
                'gradereport_builder_template', $params, 'name DESC', 'id,name'
            );

            if ($templates) {
                $options[$labels[$contextlevel]] = $templates;
            }
        }

        return $options;
    }
}
