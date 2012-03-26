<?php

require_once $CFG->dirroot . '/grade/report/lib.php';
require_once $CFG->libdir . '/quick_template/lib.php';

class grade_report_gradebook_builder extends grade_report {
    function process_data($data) {
        global $DB;

        $options = $this->get_available_options();
        $contextlevel = $data['contextlevel'];

        if (!isset($options[$contextlevel])) {
            // Naturally assume this template is for the user
            $contextlevel = CONTEXT_USER;
        }

        $template = new stdClass;

        $template->name = $data['name'];
        $template->data = $data['data'];

        $template->contextlevel = $contextlevel;
        $template->instanceid = $this->determine_instanceid($contextlevel);

        if (isset($data['template'])) {
            $id = $DB->insert_record('gradereport_builder_template', $template);
            $template->id = $id;
        } else {
            $template->id = $data['template'];
            $DB->update_record('gradereport_builder_template', $template);
        }

        redirect(new moodle_url('/grade/report/gradebook_builder/index.php', array(
            'id' => $this->course->id,
            'template' => $template->id
        )));
    }

    function process_action($target, $action) {
    }

    function __construct($courseid, $gpr, $context, $template = null) {
        parent::__construct($courseid, $gpr, $context);

        if (!$template) {
            $template = new stdClass;
            $template->name = 'New Template';
            $template->contextlevel = CONTEXT_USER;
            $template->instanceid = $this->determine_instanceid(CONTEXT_USER);
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
            'template' => $this->template,
            'templates' => $this->get_templates(),
            'save_options' => $this->get_available_options()
        );

        quick_template::render('index.tpl', $data);
    }

    function determine_instanceid($contextlevel) {
        global $USER;

        switch ($contextlevel) {
            case CONTEXT_USER: return $USER->id;
            case CONTEXT_COURSECAT: return $this->course->category;
            case CONTEXT_SYSTEM: return 0;
        }
        print_error('undefined_context', 'gradereport_gradebook_builder');
    }

    function determine_label($contextlevel) {
        global $USER;

        switch ($contextlevel) {
            case CONTEXT_USER: return fullname($USER);
            case CONTEXT_SYSTEM: return get_string('coresystem');
            case CONTEXT_COURSECAT:
                global $DB;
                return $DB->get_field('course_categories', 'name', array(
                    'id' => $this->course->category
                ));
            default: '';
        }
    }

    function determine_context($contextlevel) {
        return get_context_instance(
            $contextlevel, $this->determine_instanceid($contextlevel)
        );
    }

    function get_available_options() {
        global $DB;

        $_s = function($key, $a=null) {
            return get_string($key, 'gradereport_builder_template', $a);
        };

        $options = array(CONTEXT_USER => $_s('save_user'));

        $context = $this->determine_context(CONTEXT_COURSECAT);
        if (has_capability('moodle/grade:edit', $context)) {
            $name = $this->determine_label(CONTEXT_COURSECAT);
            $options[CONTEXT_COURSECAT] = $_s('save_category', $name);
        }

        $context = $this->determine_context(CONTEXT_SYSTEM);
        if (has_capability('moodle/grade:edit', $context)) {
            $options[CONTEXT_SYSTEM] = $_s('save_system');
        }

        return $options;
    }

    function get_templates() {
        global $USER, $DB;

        $levels = array(CONTEXT_USER, CONTEXT_COURSECAT, CONTEXT_SYSTEM);

        $options = array();
        // Gather templates at respective context levels
        foreach ($levels as $contextlevel) {
            $params = array(
                'contextlevel' => $contextlevel,
                'instanceid' => $this->determine_instanceid($contextlevel)
            );

            $templates = $DB->get_records_menu(
                'gradereport_builder_template', $params, 'name DESC', 'id,name'
            );

            if ($templates) {
                $label = $this->determine_label($contextlevel);
                $options[$label] = $templates;
            }
        }

        return $options;
    }
}
