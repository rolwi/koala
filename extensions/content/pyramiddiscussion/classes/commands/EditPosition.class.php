<?php
namespace Pyramiddiscussion\Commands;
class EditPosition extends \AbstractCommand implements \IFrameCommand, \IAjaxCommand {

	private $params;
	private $id;
	private $pyramiddiscussion;

	public function validateData(\IRequestObject $requestObject) {
		return true;
	}

	public function processData(\IRequestObject $requestObject) {
		if ($requestObject instanceof \UrlRequestObject) {
			$this->params = $requestObject->getParams();
			isset($this->params[0]) ? $this->pyramiddiscussion = $this->params[0]: "";
			isset($this->params[1]) ? $this->id = $this->params[1]: "";
		} else if ($requestObject instanceof \AjaxRequestObject) {
			$this->params = $requestObject->getParams();
			isset($this->params["id"]) ? $this->id = $this->params["id"]: "";
		}
	}

	public function frameResponse(\FrameResponseObject $frameResponseObject) {
		return $frameResponseObject;
	}
	
	public function ajaxResponse(\AjaxResponseObject $ajaxResponseObject) {
		
		$pyramidPosition = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->id);
		$user = $GLOBALS["STEAM"]->get_current_steam_user();
		
		if ($this->params["action"] == "join") {
			// changing group (only) in the group choosing phase
			$pyramid = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->params["pyramid"]);
			$phase = $pyramid->get_attribute("PYRAMIDDISCUSSION ACTCOL");
			if ($phase == 0) {
				if ($this->params["formergroup"] == $this->params["newgroup"]) {
					$formergroup = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->params["formergroup"]);
					$formergroup->remove_member($user);
				} else if ($this->params["formergroup"] == 0) {
					$newgroup = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->params["newgroup"]);
					$newgroup->add_member($user);
				} else {
					$formergroup = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->params["formergroup"]);
					$formergroup->remove_member($user);
					$newgroup = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->params["newgroup"]);
					$newgroup->add_member($user);
				}
			}
			$ajaxResponseObject->setStatus("ok");
			return $ajaxResponseObject;
		} else {
			$read_position_states = array();
			$read_position_states[$user->get_id()] = 1;
			$pyramidPosition->set_attribute("PYRAMIDDISCUSSION_POS_READ_STATES", $read_position_states);
			
			$column = $pyramidPosition->get_attribute("PYRAMIDDISCUSSION_COLUMN");
			$row = $pyramidPosition->get_attribute("PYRAMIDDISCUSSION_ROW");
			
			$dialog = new \Widgets\Dialog();
			$dialog->setTitle("Bearbeite Position " . $column . "-" . $row);
			$dialog->setWidth("600");
			$clearer = new \Widgets\Clearer();
			
			$titleInput = new \Widgets\TextInput();
			$titleInput->setLabel("Titel");
			$titleInput->setData($pyramidPosition);
			$titleInput->setContentProvider(\Widgets\DataProvider::attributeProvider("OBJ_DESC"));
			$dialog->addWidget($titleInput);
			$dialog->addWidget($clearer);
			
			$textareaWidget = new \Widgets\Textarea();
			$textareaWidget->setTextareaClass("mce-small");
			$textareaWidget->setWidth("600");
			$textareaWidget->setData($pyramidPosition);
			$textareaWidget->setContentProvider(\Widgets\DataProvider::contentProvider());
			
			$dialog->addWidget($textareaWidget);
			$dialog->addWidget($clearer);
			
			$ajaxResponseObject->addWidget($dialog);
			$ajaxResponseObject->setStatus("ok");
			return $ajaxResponseObject;
		}
	}
}
?>