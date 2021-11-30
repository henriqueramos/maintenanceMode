<?php

/**
 * @defgroup plugins_generic_maintenancemode
 */

/**
 * @file plugins/generic/maintenanceMode/index.php
 *
 * Copyright (c) 2013-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Wrapper for Maintenance Mode plugin
 *
 * @ingroup plugins_generic_maintenancemode
 */

require_once('MaintenanceModePlugin.inc.php');

return new MaintenanceModePlugin();
