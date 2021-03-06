<?php
class Explorer extends AbstractExtension implements IIconBarExtension{
	
	public function getName() {
		return "Explorer";
	}
	
	public function getDesciption() {
		return "Extension for explorer view.";
	}
	
	public function getVersion() {
		return "v1.0.0";
	}
	
	public function getAuthors() {
		$result = array();
		$result[] = new Person("Dominik", "Niehus", "nicke@uni-paderborn.de");
		return $result;
	}
	
	public function getPriority() {
		return -20;
	}
	
	public function getIconBarEntries() {
		$currentUser = $GLOBALS["STEAM"]->get_current_steam_user();
		$trashbin = $currentUser->get_attribute(USER_TRASHBIN);
		$trashbinModel = new \Explorer\Model\Trashbin($trashbin);
		//$trashbinCount = count($trashbin->get_inventory());
		$trashbinCount = 1;
		$clipboardModel = new \Explorer\Model\Clipboard($currentUser);
		//$clipboardCount = count($currentUser->get_inventory());
		$clipboardCount = 1;
		return array(array("name"=>"<div id=\"clipboardIconbarWrapper\">".$clipboardModel->getIconbarHtml()."</div>", 
		                   "menu"=> ($clipboardCount > 0 ) ? array(array("name"=>"Objekte hier einfügen", "onclick"=>"sendRequest('Paste', {'env':jQuery('#environment').attr('value')}, '', 'popup', null, null, 'explorer');"), 
		                                 array("name"=>"Zwischenablage leeren", "onclick"=>"sendRequest('EmptyClipboard', {}, '', 'popup', null, null, 'explorer');")) : ""), 
		             array("name"=> "<div id=\"trashbinIconbarWrapper\">".$trashbinModel->getIconbarHtml()."</div>", 
		                   "menu"=> ($trashbinCount > 0 ) ? array(array("name"=>"Papierkorb leeren", "onclick"=>"sendRequest('EmptyTrashbin', {}, '', 'popup', null, null, 'explorer');"), 
		                                 array("name"=>"Papierkorb öffnen")) : "")
		);
	}
	
	public function getCurrentObject(UrlRequestObject $urlRequestObject) {
		$params = $urlRequestObject->getParams();
		$id = $params[0];
		if (isset($id)) {
			if (!isset($GLOBALS["STEAM"])) {
				return null;
			}
			$object = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $id);
			if (!($object instanceof steam_object)) {
				return null;
			}
			$type = getObjectType($object);
			if (array_search($type, array("referenceFolder", "container", "userHome", "groupWorkroom", "room", "document")) !== false) {
				return $object;
			}
		} else {
			$currentUser = $GLOBALS["STEAM"]->get_current_steam_user();
			$object = $currentUser->get_workroom();
			return $object;
		}
		return null;
	}
}
?>