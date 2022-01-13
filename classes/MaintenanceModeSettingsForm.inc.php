<?php

/**
 * @file plugins/generic/maintenanceMode/MaintenanceModeSettingsForm.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class MaintenanceModeSettingsForm
 * @ingroup plugins_generic_maintenanceMode
 *
 * @brief Form for site admin to specify site redirect URL
 */

import('lib.pkp.classes.form.Form');

class MaintenanceModeSettingsForm extends Form
{
    /** @var MaintenanceModePlugin */
    protected $_plugin;

    /**
     * Constructor
     * @param $plugin
     */
    public function __construct($plugin)
    {
        $this->_plugin = $plugin;

        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
        $this->addCheck(new FormValidatorCustom($this, 'customUrl', FORM_VALIDATOR_OPTIONAL_VALUE, 'plugins.generic.maintenancemode.settings.customUrl.errorMessage', [&$this, '_isCustomUrlValid']));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Initialize form data.
     */
    public function initData()
    {
        $plugin = $this->_plugin;

        $this->setData('useCustomUrl', $plugin->getSetting(CONTEXT_SITE, 'useCustomUrl'));
        $this->setData('customUrl', $plugin->getSetting(CONTEXT_SITE, 'customUrl'));
    }

    /**
     * Assign form data to user-submitted data.
     */
    public function readInputData()
    {
        $this->readUserVars(['useCustomUrl', 'customUrl']);
    }

    /**
     * Fetch the form.
     * @copydoc Form::fetch()
     */
    public function fetch($request)
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->_plugin->getName());
        return parent::fetch($request);
    }

    /**
     * @copydoc::Form::execute()
     */
    public function execute(...$functionArgs)
    {
        $plugin = $this->_plugin;
        $plugin->updateSetting(CONTEXT_SITE, 'useCustomUrl', $this->getData('useCustomUrl'));
        $plugin->updateSetting(CONTEXT_SITE, 'customUrl', $this->getData('customUrl'));

        parent::execute(...$functionArgs);
    }

    /**
     * Validation check for custom URL
     *
     * @param string $fieldValue
     * @return bool
     */
    public function _isCustomUrlValid(string $fieldValue): bool
    {
        // Naive check to make sure URL includes protocol
        $hasHttpProtocol = preg_match('/https*:\/\/:.*/g', $fieldValue);

        if ($hasHttpProtocol !== false || !$this->_plugin->customUrlIsValid($fieldValue)) {
            return false;
        }

        return true;
    }
}
