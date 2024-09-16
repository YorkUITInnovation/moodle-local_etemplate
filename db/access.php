<?php
$capabilities = array(
    /**
     * YULearn Course capabilites
     */
    'local/etemplate:view_system_reserved' => array(
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    )
);