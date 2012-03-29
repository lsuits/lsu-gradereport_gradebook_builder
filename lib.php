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
            $data['template'] = null;
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

        // Saved template, let them confirm it
        redirect(new moodle_url('/grade/report/gradebook_builder/preview.php', array(
            'id' => $this->course->id,
            'template' => $template->id
        )));
    }

    function build_gradebook($courseid, $template) {
        global $DB;
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUSt_EXIST);

        $obj = json_decode($template->data);

        $aggregation = $obj->aggregation;

        if (!$this->get_aggregation_label($aggregation)) {
            return 'invalid_aggregation';
        }

        foreach ($obj->categories as $grade_category) {
            $category = new grade_category(array('courseid' => $courseid));
            $category->apply_default_settings();
            $category->apply_forced_settings();

            $category->fullname = $grade_category->name;
            $category->aggregation = $aggregation;
            $category->insert();

            $cat_item = $category->load_grade_item();
            $cat_item->aggregationcoef = $grade_category->weight;
            $cat_item->update();

            foreach ($grade_category->items as $grade_item) {
                if ($grade_item->itemtype == 'manual') {
                    $item = self::build_manual_item($courseid, $grade_item);
                } else {
                    $item = self::build_mod_item($course, $grade_item);
                }
                $item->set_parent($category->id, 'gradebook_builder');
            }
        }

        return true;
    }

    function default_course_module($course, $item) {
        $newcm = new stdClass;
        $newcm->course = $course->id;
        $newcm->module = $item->itemmodule;
        $newcm->instance = 0;
        $newcm->visible = 0;
        $newcm->groupmode = $course->groupmode;
        $newcm->groupingid = 0;

        $newmc->id = add_course_module($newcm);
        return $newcm;
    }

    function default_graded_module($course, $item) {
        $cm = self::default_course_module($course, $item);

        $module = new stdClass;
        $module->course = $course->id;
        $module->name = $item->name;
        $module->intro = '';
        $module->grade = $item->grademax;
        $module->coursemodule = $cm->id;

        $add_instance = $item->itemmodule . '_add_instance';
        return $add_instance($module);
    }

    function build_mod_item($course, $item) {
        $instanceid = self::default_course_module($course, $item);

        $grade_item = new grade_item(array(
            'courseid' => $course->id,
            'itemtype' => 'mod',
            'itemmodule' => $item->itemtype,
            'iteminstance' => $instanceid
        ));

        $grade_item->aggregationcoef = $item->weight;
        $grade_item->update();

        return $grade_item;
    }

    function build_manual_item($courseid, $item) {
        $grade_item = new grade_item(array(
            'courseid' => $courseid,
            'itemtype' => 'manual'
        ));

        $grade_item->aggregationcoef = $item->weight;
        $grade_item->insert();

        return $grade_item;
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

        $PAGE->requires->js('/grade/report/gradebook_builder/jquery.js');
        $PAGE->requires->js('/grade/report/gradebook_builder/app.js');
    }

    function output() {
        $data = array(
            'template' => $this->template,
            'templates' => $this->get_templates(),
            'save_options' => $this->get_available_options(),
            'aggregations' => $this->get_available_aggregations()
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

    function get_available_aggregations() {
        $visibles = explode(',', get_config('moodle', 'grade_aggregation_visible'));
        $options = array();

        foreach ($visibles as $aggregation) {
            $options[$aggregation] = $this->get_aggregation_label($aggregation);
        }

        return $options;
    }

    function get_aggregation_label($aggregation) {
        $_s = function($key) { return get_string($key, 'grades'); };
        switch ($aggregation) {
            case GRADE_AGGREGATE_MEAN: return $_s('aggregatemean');
            case GRADE_AGGREGATE_WEIGHTED_MEAN: return $_s('aggregateweightedmean');
            case GRADE_AGGREGATE_WEIGHTED_MEAN2: return $_s('aggregateweightedmean2');
            case GRADE_AGGREGATE_EXTRACREDIT_MEAN: return $_s('aggregateextracreditmean');
            case GRADE_AGGREGATE_MEDIAN: return $_s('aggregatemedian');
            case GRADE_AGGREGATE_MIN: return $_s('aggregationmin');
            case GRADE_AGGREGATE_MAX: return $_s('aggregationmax');
            case GRADE_AGGREGATE_MODE: return $_s('aggregationmode');
            case GRADE_AGGREGATE_SUM: return $_s('aggregationsum');
            default: return null;
        }
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
