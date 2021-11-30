<?php

/**
 * @file MaintenanceModePlugin.inc.php
 *
 * Copyright (c) 2013-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class MaintenanceModePlugin
 * @ingroup plugins_generic_maintenancemode
 *
 * @brief Maintenance Mode's plugin class
 */

use PKP\core\Core;
use PKP\plugins\GenericPlugin;

class MaintenanceModePlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);

        if (defined('RUNNING_UPGRADE')) {
            return $success;
        }

        if ($success && $this->getEnabled()) {
            $this->maintenanceModeChecker();
        }

        return $success;
    }

    /**
     * Get the display name of this plugin
     *
     * @return string
     */
    public function getDisplayName()
    {
        return __('plugins.generic.maintenancemode.displayName');
    }

    /**
     * Get the description of this plugin
     *
     * @return string
     */
    public function getDescription()
    {
        return __('plugins.generic.maintenancemode.description');
    }

    protected function maintenanceModeChecker(): void
    {
        $file = Core::getBaseDir() . DIRECTORY_SEPARATOR . '.maintenance';

        if (file_exists($file)) {
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            die(__('plugins.generic.maintenancemode.underMaintenance'));
        }
    }
}
