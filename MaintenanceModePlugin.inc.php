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

import('lib.pkp.classes.plugins.GenericPlugin');

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

    /**
     * @copydoc Plugin::getActions()
     */
    public function getActions($request, $verb)
    {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
            $this->getEnabled()?array(
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ):array(),
            parent::getActions($request, $verb)
        );
    }

    /**
     * @copydoc Plugin::manage()
     */
    public function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON,  LOCALE_COMPONENT_PKP_MANAGER);
                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->registerPlugin('function', 'plugin_url', array($this, 'smartyPluginUrl'));

                $this->import('classes.MaintenanceModeSettingsForm');
                $form = new MaintenanceModeSettingsForm($this);

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }
                return new JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }

    protected function maintenanceModeChecker(): void
    {
        $file = Core::getBaseDir() . DIRECTORY_SEPARATOR . '.maintenance';

        if (file_exists($file)) {
            $useCustomUrl = $this->getSetting(CONTEXT_SITE, 'useCustomUrl');
            $customUrl = $this->getSetting(CONTEXT_SITE, 'customUrl');

            if ($useCustomUrl && $this->customUrlIsValid($customUrl)) {
                header('Location: ' . $customUrl, true, 307);
				return;
            }
			header('HTTP/1.1 503 Service Temporarily Unavailable');
            die(__('plugins.generic.maintenancemode.underMaintenance'));
        }
    }

    /**
     * Check if desired redirect is uses base path that's being redirected
     *
     * @param string|null $customUrl
     * @return bool
     */
    public function customUrlIsValid(?string $customUrl): bool
    {
        $baseUrl = Config::getVar('general', 'base_url');

        if (!$customUrl || strpos($customUrl, $baseUrl) !== false) {
            return false;
        }

        return true;
    }
}
