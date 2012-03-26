<?php

$capabilities = array(
    'gradereport/gradebook_builder:view' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'context_level' => CONTEXT_COURSE,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'edditing_teacher' => CAP_ALLOW
        )
    )
);
