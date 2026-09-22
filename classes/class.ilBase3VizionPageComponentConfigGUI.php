<?php declare(strict_types=1);

use Base3\Api\IAssetResolver;
use Base3\Api\IDisplay;
use Base3\Base3Ilias\PageComponent\AbstractPageComponentConfigGUI;

/**
 * @ilCtrl_isCalledBy ilBase3VizionPageComponentConfigGUI: ilObjComponentSettingsGUI
 */
class ilBase3VizionPageComponentConfigGUI extends AbstractPageComponentConfigGUI {

	/**
	 * Execute command routed by ilPluginConfigGUI::executeCommand().
	 *
	 * @throws ilCtrlException
	 */
	public function performCommand(string $cmd): void {
		$this->init();

		// Include client scripts.
		$assetResolver = $this->dic[IAssetResolver::class];
		$this->tpl->addJavaScript($assetResolver->resolve('plugin/ClientStack/assets/assetloader/assetloader.min.js'));
		$this->tpl->addJavaScript($assetResolver->resolve('plugin/ClientStack/assets/jqueryui/jquery-ui.js'));
		$this->tpl->addCss($assetResolver->resolve('plugin/ClientStack/assets/jqueryui/jquery-ui.css'));

		// Tabs
		$this->tabs->addTab("general", $this->txt("tab_general"), $this->ctrl->getLinkTarget($this, "general"));
		$this->tabs->addTab("schema", $this->txt("tab_schema"), $this->ctrl->getLinkTarget($this, "schema"));

		if (!in_array($cmd, ["general", "schema"], true)) {
			$cmd = "general";
		}

		$this->tabs->activateTab($cmd);
		$this->{$cmd}();
	}

	/**
	 * Tab: General
	 */
	protected function general(): void {
		$this->tpl->setContent(
			"<h2>Base3Vizion PageComponent</h2>" .
			"<p>Dummy content (General tab). Configuration UI will follow.</p>"
		);
	}

	/**
	 * Tab: Schema
	 */
	protected function schema(): void {

		$html = '<h2>Database Schema</h2>';

		$displays = $this->classmap->getInstances([ 'interface' => IDisplay::class, 'name' => 'datahawkschemadisplay' ]);
		if (empty($displays)) {
			$this->tpl->setContent('Schema display not found.');
			return;
		}
		$display = $displays[0];

		$display->setData('');
		$html .= $display->getOutput('html');

		$this->tpl->setContent($html);
	}
}
