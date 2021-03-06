<?php
namespace Explorer\Commands;
class NewDocumentPlainForm extends \AbstractCommand implements \IFrameCommand, \IAjaxCommand {
	
	private $params;
	private $id;
	
	public function getExtension() {
		return \DocumentPlainObject::getInstance();
	}
	
	public function validateData(\IRequestObject $requestObject) {
		return true;
	}
	
	public function processData(\IRequestObject $requestObject) {
		if ($requestObject instanceof \UrlRequestObject) {
			$this->params = $requestObject->getParams();
			isset($this->params[0]) ? $this->id = $this->params[0]: "";
		} else if ($requestObject instanceof \AjaxRequestObject) {
			$this->params = $requestObject->getParams();
			isset($this->params["id"]) ? $this->id = $this->params["id"]: "";
		}
	}
	
	public function ajaxResponse(\AjaxResponseObject $ajaxResponseObject) {
		$ajaxResponseObject->setStatus("ok");
		
		$textInput = new \Widgets\TextInput();
		$ajaxForm = new \Widgets\AjaxForm();
		$ajaxForm->addWidget($textInput);
		$ajaxForm->setSubmitCommand("CreateDocumentPlain");
		$ajaxForm->setSubmitNamespace("Explorer");
		
		$ajaxForm->setHtml(<<<END
<style type="text/css">
.attribute {
  clear: left;
  padding: 5px 2px 5px 2px;
}

.attributeName {
  float: left;
  padding-right: 20px;
  text-align: right;
  width: 80px;
}

.attributeValue {
  float: left;
  width: 300px;
}

.attributeValue .text, .attributeValue textarea {
  width: 300px;
}

.attributeValueColumn {
  float: left;
  position: relative;
  text-align: center;
}

</style>
<input type="hidden" name="id" value="{$this->id}">
<div class="attribute">
	<div class="attributeName">Dateiname:</div>
	<div class="attributeValue"><input type="text" class="text" value="" name="name"></div>
</div>
<br>
END
);
		$ajaxResponseObject->addWidget($ajaxForm);
		return $ajaxResponseObject;
	}
	
	public function frameResponse(\FrameResponseObject $frameResponseObject) {
		
	}
}
?>