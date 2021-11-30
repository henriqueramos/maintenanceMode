{**
 * plugins/generic/maintenanceMode/settingsForm.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Maintenance MOde plugin settings
 *
 *}
<script>
	$(function() {ldelim}
		// Attach the form handler
		$('#maintenanceModeSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim})
</script>

<form class="pkp_form" id="maintenanceModeSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	<div id="maintenanceModeSettings">
		<h3>{translate key="plugins.generic.maintenancemode.settings"}</h3>

        {csrf}
        {include file="common/formErrors.tpl"}

		{fbvFormArea id="maintenanceModeOptions"}
			{fbvFormSection for="useCustomUrl" list=true description="plugins.generic.maintenancemode.settings.useCustomUrl.description"}
				{fbvElement type="checkbox" id="useCustomUrl" value="1" checked=$useCustomUrl label="plugins.generic.maintenancemode.settings.useCustomUrl"}
			{/fbvFormSection}
			{fbvFormSection for="customUrl" list=true description="plugins.generic.maintenancemode.settings.customUrl.description"}
				{fbvElement type="text" id="customUrl" value=$customUrl label="plugins.generic.maintenancemode.settings.customUrl"}
			{/fbvFormSection}
		{/fbvFormArea}

        {fbvFormButtons id="maintenanceModeSettingsSettingsFormSubmit"}

	</div>
</form>
