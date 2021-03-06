<?php
namespace PortletMedia\Commands;
class Index extends \AbstractCommand implements \IFrameCommand, \IIdCommand {
	
	private $params;
	private $id;
	private $content;
	private $rawHtmlWidget;
	
	public function validateData(\IRequestObject $requestObject) {
		return true;
	}
	
	public function processData(\IRequestObject $requestObject){
		$objectId=$requestObject->getId();
		$portletInstance = \PortletMedia::getInstance();
		$portletPath = $portletInstance->getExtensionPath();
		$portlet = $portletObject = \steam_factory::get_object( $GLOBALS["STEAM"]->get_id(), $objectId );
		
		//icon
		$referIcon = \Portal::getInstance()->getAssetUrl() . "icons/refer_white.png";
		
		//reference handling
		$params = $requestObject->getParams();
		if(isset($params["referenced"]) && $params["referenced"]==true){
			$portletIsReference = true;
			$referenceId = $params["referenceId"];
		}else{
			$portletIsReference = false;
		}
		
		//hack
		include_once(PATH_BASE."koala-core/lib/bid/slashes.php");
		
		
		//get content of portlet
		$content = $portlet->get_attribute("bid:portlet:content");
		if(is_array($content) && count($content) > 0){
			array_walk($content, "_stripslashes");
		} else {
			$content = array();
		}
		
		if(sizeof($content) > 0){
			$portletFileName=$portletPath."/ui/html/index.html";
			$tmpl = new \HTML_TEMPLATE_IT();
			$tmpl->loadTemplateFile($portletFileName);
			
			//popupmenu
			if(!$portletIsReference && $portlet->check_access_write($GLOBALS["STEAM"]->get_current_steam_user())){
				$popupmenu = new \Widgets\PopupMenu();
				$popupmenu->setData($portlet);
				$popupmenu->setNamespace("PortletMedia");
				$popupmenu->setElementId("portal-overlay");
				$tmpl->setVariable("POPUPMENU", $popupmenu->getHtml());
			}
			
			if($portletIsReference && $portlet->check_access_write($GLOBALS["STEAM"]->get_current_steam_user())){
				$popupmenu = new \Widgets\PopupMenu();
				$popupmenu->setData($portlet);
				$popupmenu->setNamespace("Portal");
				$popupmenu->setElementId("portal-overlay");
				$popupmenu->setParams(array(array("key" => "sourceObjectId", "value" => $portlet->get_id()),
											array("key" => "linkObjectId", "value" => $referenceId)
											));
				$popupmenu->setCommand("PortletGetPopupMenuReference");
				$tmpl->setVariable("POPUPMENU", $popupmenu->getHtml());
			}
			
			
		
			$tmpl->setVariable("EDIT_BUTTON","");
			$tmpl->setVariable("PORTLET_ID",$portlet->get_id());
			$tmpl->setVariable("HEADLINE",$content["headline"]);
			
			//refernce icon
			if($portletIsReference){
				$tmpl->setVariable("REFERENCE_ICON","<img src='{$referIcon}'>");
			}
			
			$tmpl->setVariable("URL",$content["url"]);
			$tmpl->setVariable("DESCRIPTION",$content["description"]);
				
		
			$media_type = $content["media_type"];
			if ($media_type == "image"){
				$tmpl->setCurrentBlock("image");
				$tmpl->parse("MEDIA_ELEMENT", "image");
				$tmpl->parse("image");
			}
			else if ($media_type == "movie") {
				$tmpl->setCurrentBlock("movie");
				$width = str_replace(array("px", "%"), "", $portlet->get_environment()->get_attribute("bid:portal:column:width")) - 10;
				$media_player = $portletInstance->getAssetUrl() . 'mediaplayer.swf';
				$tmpl->setVariable("MEDIA_PLAYER", $media_player);
				$tmpl->setVariable("MEDIA_PLAYER_WIDTH", $width);
				$tmpl->setVariable("MEDIA_PLAYER_HEIGHT", round($width * 3/4));
				$tmpl->parse("movie");
			}
			else if ($media_type == "audio") {
				$tmpl->setCurrentBlock("audio");
				$width = str_replace(array("px", "%"), "", $portlet->get_environment()->get_attribute("bid:portal:column:width")) - 10;
				$media_player = $portletInstance->getAssetUrl() . 'emff_lila_info.swf';
				$tmpl->setVariable("MEDIA_PLAYER", $media_player);
				$tmpl->setVariable("MEDIA_PLAYER_WIDTH", $width);
				$tmpl->setVariable("MEDIA_PLAYER_HEIGHT", round($width * 11/40));
				$tmpl->parse("audio");
			}
			if ($portlet->check_access_write($GLOBALS["STEAM"]->get_current_steam_user())){
				$tmpl->setCurrentBlock("BLOCK_EDIT_BUTTON");
				$tmpl->setVariable("PORTLET_ID_EDIT",$portlet->get_id());
				$tmpl->parse("BLOCK_EDIT_BUTTON");
			}
		
			//output
			$htmlBody=$tmpl->get();
		}
		else{
			//output for no content
			$htmlBody="";
		}
		$this->content=$htmlBody;
		
		//widgets
		$outputWidget = new \Widgets\RawHtml();
		$outputWidget->setHtml($htmlBody);
		
		//popummenu
		$popupmenu = new \Widgets\PopupMenu();
		$popupmenu->setData($portlet);
		$popupmenu->setNamespace("PortletMedia");
		$popupmenu->setElementId("portal-overlay");
		$outputWidget->addWidget($popupmenu);
		
		$this->rawHtmlWidget = $outputWidget;
	}
	
	public function idResponse(\IdResponseObject $idResponseObject) {
		$idResponseObject->addWidget($this->rawHtmlWidget);
		return $idResponseObject;
	}
	
	public function frameResponse(\FrameResponseObject $frameResponseObject) {
		$frameResponseObject->setTitle("Portal");
		$frameResponseObject->addWidget($this->rawHtmlWidget);
		return $frameResponseObject;
	}
	
}
?>