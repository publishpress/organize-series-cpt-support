<?php
/**
 * Contains all the legacy includes (and hooks) for the plugin.  Eventually this will make its way into the new refactor
 * of the plugin. But while in transition this allows these legacy files to be included as necessary.
 */
//get files we need.
require_once OS_CPT_PATH . 'class/OS_CPT_Support.class.php';
//load the addon
$os_cpt_support = new OS_CPT_Support();