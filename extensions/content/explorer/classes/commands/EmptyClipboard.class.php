<?php
namespace Explorer\Commands;
class EmptyClipboard extends \AbstractCommand implements \IAjaxCommand {
	
	private $params;
	private $id;
	private $elements;
	private $clipboard;
	
	public function validateData(\IRequestObject $requestObject) {
		return true;
	}
	
	public function processData(\IRequestObject $requestObject) {		
		$this->params = $requestObject->getParams();
		$this->clipboard = $GLOBALS["STEAM"]->get_current_steam_user();
		if (isset($this->params["id"])) {
			$this->id = $this->params["id"];
			$object = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->id);
			$object->delete();
		} else {
			$this->elements = $this->clipboard->get_inventory();
		}
	}
	
	public function ajaxResponse(\AjaxResponseObject $ajaxResponseObject) {
		if (!isset($this->id)) {
			$ajaxResponseObject->setStatus("ok");
			$jswrapper = new \Widgets\JSWrapper();
			$ids = "";
			$elements = "";
			foreach ($this->elements as $key => $element) {
				if (count($this->elements) > $key + 1) {
					$ids .= "{\"id\":\"" . $element->get_id() . "\"}, ";
					$elements .= "\"\", ";
				} else {
					$ids .= "{\"id\":\"" . $element->get_id() . "\"}";
					$elements .= "\"\"";
				}
			}
			$js = "sendMultiRequest('EmptyClipboard', jQuery.parseJSON('[$ids]'), jQuery.parseJSON('[$elements]'), 'updater', null, null, 'explorer', 'Leere Zwischenablage ...', 0, " . count($this->elements) . ");";
			$jswrapper->setJs($js);
			$ajaxResponseObject->addWidget($jswrapper);
			return $ajaxResponseObject;
		} else {
			$ajaxResponseObject->setStatus("ok");
			$clipboardModel = new \Explorer\Model\Clipboard($this->clipboard);
			$jswrapper = new \Widgets\JSWrapper();
			$js = "document.getElementById('clipboardIconbarWrapper').innerHTML = '" . $clipboardModel->getIconbarHtml() . "';";
			$jswrapper->setJs($js);
			$ajaxResponseObject->addWidget($jswrapper);
			return $ajaxResponseObject;
		}
	}
}
?>