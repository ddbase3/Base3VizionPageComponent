<?php declare(strict_types=1);

use Base3\Base3Ilias\PageComponent\AbstractPageComponentPluginGUI;
use Vizion\Api\IReportDisplay;

/**
 * @ilCtrl_isCalledBy ilBase3VizionPageComponentPluginGUI: ilPCPluggedGUI
 */
class ilBase3VizionPageComponentPluginGUI extends AbstractPageComponentPluginGUI {

	protected function getPageComponentName(): string {
		return 'BASE3 Vizion';
	}

	protected function getPageComponentDesc(): string {
		return 'BASE3 Vizion Page Component';
	}

	protected function getDefaultProps(): array {
		// Report identifier (e.g. "user" -> loads "user.json" via config provider).
		return [
			'report' => 'user',
		];
	}

	protected function setFormContent(ilPropertyFormGUI $form, array $props): void {
		// Simple textbox for the report identifier (e.g. "user").
		$reportControl = new ilTextInputGUI('Report', 'report');
		$reportControl->setValue((string)($props['report'] ?? ''));
		$form->addItem($reportControl);
	}

	protected function getPresentationHtml(array $a_properties, string $plugin_version): string {

		// Include client scripts.
		$this->tpl->addJavaScript('components/Base3/ClientStack/assetloader/assetloader.min.js');

		// Read report identifier from properties.
		$report = trim((string)($a_properties['report'] ?? ''));
		if ($report === '') {
			return 'Missing report identifier.';
		}

		// Get the (general) report display.
		/** @var IReportDisplay|null $reportDisplay */
		$reportDisplay = $this->classmap->getInstanceByInterfaceName(IReportDisplay::class, 'generalreportdisplay');
		if (!$reportDisplay) {
			// Fallback: take the first available implementation.
			$displays = $this->classmap->getInstances(['interface' => IReportDisplay::class]);
			if (empty($displays)) {
				return 'ReportDisplay not found.';
			}
			$reportDisplay = $displays[0];
		}

		// Configure and output.
		$reportDisplay->setData($report);
		return (string)$reportDisplay->getOutput('html');
	}
}
