<?php
class PortletMedia extends AbstractExtension implements IObjectExtension{
	
	public function getName() {
		return "PortletMedia";
	}
	
	public function getDesciption() {
		return "Extension for portlet media.";
	}
	
	public function getVersion() {
		return "v1.0.0";
	}
	
	public function getAuthors() {
		$result = array();
		$result[] = new Person("Marcel", "Jakoblew", "mjako@uni-paderborn.de");
		return $result;
	}
	
	public function getObjectReadableName() {
		return "Komponente Medienelement";
	}
	
	public function getObjectReadableDescription() {
		return "Komponente Medienelement";
	}
	
	public function getObjectIconUrl() {
		return Explorer::getInstance()->getAssetUrl() . "icons/mimetype/portlet.png";
	}
	
	public function getCreateNewCommand(IdRequestObject $idEnvironment) {
		return new \PortletMedia\Commands\CreateNewForm();
	}

	public function getCommandByObjectId(IdRequestObject $idRequestObject){
		$portletObject = steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $idRequestObject->getId());
		
		$portletType = $portletObject->get_attribute("bid:portlet");
		if (!($portletType==="media")) return false;
		if ($idRequestObject->getMethod() == "view") {
			return new \PortletMedia\Commands\Index();
		}
	}
}
?>