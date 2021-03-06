<?php
namespace PortletHeadline\Commands;
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
		$portlet= $portletObject = \steam_factory::get_object( $GLOBALS["STEAM"]->get_id(), $objectId );
		
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
		
		//get singleton and portlet path
		$portletInstance = \PortletHeadline::getInstance();
		$portletPath = $portletInstance->getExtensionPath();
		
		if(sizeof($content) > 0){
			$portletFileName=$portletPath."/ui/html/index.html";
			$tmpl = new \HTML_TEMPLATE_IT();
			$tmpl->loadTemplateFile($portletFileName);
			
			//popupmenu
			if (!$portletIsReference && $portlet->check_access_write($GLOBALS["STEAM"]->get_current_steam_user())){
				$popupmenu = new \Widgets\PopupMenu();
				$popupmenu->setData($portlet);
				$popupmenu->setNamespace("PortletHeadline");
				$popupmenu->setElementId("portal-overlay");
				$tmpl->setVariable("POPUPMENU", $popupmenu->getHtml());
			}
			
			if ($portletIsReference && $portlet->check_access_write($GLOBALS["STEAM"]->get_current_steam_user())){
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
			
			
		
			$UBB = new \UBBCode();
			include_once(PATH_BASE."koala-core/lib/bid/derive_url.php");
			
			$tmpl->setVariable("DUMMY","");
			$tmpl->setVariable("EDIT_BUTTON","");
			$tmpl->setVariable("PORTLET_ID",$portlet->get_id());
			$tmpl->setVariable("ALIGNMENT",trim($content["alignment"]));
			$tmpl->setVariable("HEADLINE",$UBB->encode($content["headline"]));
			
			//refernce icon
			if($portletIsReference){
				$tmpl->setVariable("REFERENCE_ICON","<img src='{$referIcon}'>");
			}
			
			$tmpl->setVariable("SIZE",trim($content["size"]));
		
			if ($portlet->check_access_write($GLOBALS["STEAM"]->get_current_steam_user())){
				$tmpl->setCurrentBlock("BLOCK_EDIT_BUTTON");
				$tmpl->setVariable("PORTLET_ID_EDIT",$portlet->get_id());
				$tmpl->parse("BLOCK_EDIT_BUTTON");
			}
			
			$htmlBody=$tmpl->get();
		}
		else{
			$htmlBody="";	
		}
		
		$this->content=$htmlBody;
		
		//widgets
		$outputWidget = new \Widgets\RawHtml();
		
		//popummenu
		$outputWidget->addWidget(new \Widgets\PopupMenu());
		
		$outputWidget->setHtml($htmlBody);
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