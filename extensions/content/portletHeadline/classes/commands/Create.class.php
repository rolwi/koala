<?php
namespace PortletHeadline\Commands;
class Create extends \AbstractCommand implements \IFrameCommand, \IIdCommand, \IAjaxCommand {
	
	private $params;
	private $id;
	private $content;
	private $rawHtmlWidget;
	
	public function validateData(\IRequestObject $requestObject) {
		return true;
	}
	
	public function processData(\IRequestObject $requestObject){
		//create headline
		$params = $requestObject->getParams();
		
		$name = $params["title"];
		$column = $params["parent"];
		
		$version = "3.0";
		
		//check diffrent types of parameter
		if(is_string($column)){
			$columnObject =  \steam_factory::get_object($GLOBALS["STEAM"]->get_id(),$column);
		}else{
			$columnObject = $column;
		}
		
		
		//create object
		$headline = \steam_factory::create_container($GLOBALS["STEAM"]->get_id(), $name, $columnObject);
	    $headline_content = array(
	        "headline" => $name,
	        "alignment" => "center",
	        "size" => 15,
	    );
	    
	    $headline->set_attributes(array(
	        OBJ_DESC => $name,
	        OBJ_TYPE => "container_portlet_bid",
	        "bid:portlet" => "headline",
	        "bid:portlet:version" => $version,
	        "bid:portlet:content" => $headline_content,
	    ));
	}
	
	
	public function idResponse(\IdResponseObject $idResponseObject) {
		
	}
	
	public function frameResponse(\FrameResponseObject $frameResponseObject) {
		
	}
	
	public function ajaxResponse(\AjaxResponseObject $ajaxResponseObject) {
		$ajaxResponseObject->setStatus("ok");
		$jswrapper = new \Widgets\JSWrapper();
		$jswrapper->setJs(<<<END
		window.location.reload();
END
		);
		$ajaxResponseObject->addWidget($jswrapper);
		return $ajaxResponseObject;
	}
}
?>