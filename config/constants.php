<?php

// Lead status

if (!defined('LEAD_STATUS_PENDING')) {
    define('LEAD_STATUS_PENDING', 1);
}

if (!defined('LEAD_STATUS_FAILED')) {
    define('LEAD_STATUS_FAILED', 2);
}

if (!defined('LEAD_STATUS_CLOSED')) {
    define('LEAD_STATUS_CLOSED', 3);
}

if (!defined('LEAD_STATUS_INPROGRESS')) {
    define('LEAD_STATUS_INPROGRESS', 4);
}

if (!defined('LEAD_STATUS_COMPLETED')) {
    define('LEAD_STATUS_COMPLETED', 5);
}
if (!defined('LEAD_STATUS')) {
    define('LEAD_STATUS', [LEAD_STATUS_PENDING, LEAD_STATUS_FAILED, LEAD_STATUS_CLOSED, LEAD_STATUS_INPROGRESS,LEAD_STATUS_COMPLETED]);
}

if (!defined('USER')) {
    define('USER', 1);
}

if (!defined('MANAGER')) {
    define('MANAGER', 2);
}
if (!defined('SUBMANAGER')) {
    define('SUBMANAGER', 3);
}

if (!defined('EMPLOYEE_ROLE')) {
    define('EMPLOYEE_ROLE', [USER, MANAGER,SUBMANAGER]);
}

if (!defined('MANAGER_TYPE_INTERNAL')) {
    define('MANAGER_TYPE_INTERNAL', 1);
}

if (!defined('MANAGER_TYPE_EXTERNAL')) {
    define('MANAGER_TYPE_EXTERNAL', 2);
}

if (!defined('MANAGER_TYPE')) {
    define('MANAGER_TYPE', [MANAGER_TYPE_INTERNAL, MANAGER_TYPE_EXTERNAL]);
}