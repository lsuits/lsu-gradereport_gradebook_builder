<?php

require_once $CFG->dirroot . '/grade/report/lib.php';

class grade_report_gradebook_builder extends grade_report {
    function process_data($data) {
    }

    function process_action($target, $action) {
    }

    function __construct($courseid, $gpr, $context, $templateid) {
        parent::__construct($courseid, $gpr, $context);
    }
}
