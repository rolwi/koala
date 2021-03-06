<?php
namespace Trashbin\Commands;

class LoadContent extends \AbstractCommand implements \IAjaxCommand {

	private $params;
	private $id;
	private $objects;

	public function validateData(\IRequestObject $requestObject) {
		return true;
	}

	public function processData(\IRequestObject $requestObject) {
		$this->params = $requestObject->getParams();

		$this->id = $this->params["id"];
		$object = \steam_factory::get_object($GLOBALS["STEAM"]->get_id(), $this->id);

		if ($object && $object instanceof \steam_container) {
			$this->objects = $object->get_inventory();
		} else {
			$this->objects = array();
		}
	}

	public function ajaxResponse(\AjaxResponseObject $ajaxResponseObject) {
		$listViewer = new \Widgets\ListViewer();

		$contentProvider = new ContentProvider();
		$contentProvider->setLastPlace($this->id);

		$listViewer->setHeadlineProvider(new HeadlineProvider());
		$listViewer->setContentProvider($contentProvider);
		$listViewer->setColorProvider(new ColorProvider());
		$listViewer->setContentFilter(new ContentFilter());
		$listViewer->setContent($this->objects);
		$ajaxResponseObject->setStatus("ok");
		$ajaxResponseObject->addWidget($listViewer);
		$tipsy = new \Widgets\Tipsy();
		$ajaxResponseObject->addWidget($tipsy);
		return $ajaxResponseObject;
	}
}

class HeadlineProvider implements \Widgets\IHeadlineProvider {
	public function getHeadlines() {
		return array("", "Name", "", "Änderungsdatum", "Größe", "", "<input onChange=\"elements = jQuery('.listviewer-item > div > input'); for (i=0; i<elements.length; i++) { if (this.checked != elements[i].checked) { elements[i].click() }}\" type=\"checkbox\" ></input>");
	}

	public function getHeadLineWidths() {
		return array(25, 415, 100, 150, 80, 40, 20);
	}

	public function getHeadLineAligns() {
		return array("left", "left", "right", "right", "right", "right", "right");
	}
}

class ContentProvider implements \Widgets\IContentProvider {
	private $rawImage = 0;
	private $rawName = 1;
	private $rawMarker = 2;
	private $rawChangeDate = 3;
	private $rawSize = 4;
	private $rawMenu = 5;
	private $rawCheckbox = 6;

	private $lastPlace;

	public function setLastPlace($id){
		$this->lastPlace = $id;
	}


	public function getId($contentItem) {
		return $contentItem->get_id();
	}

	public function getCellData($cell, $contentItem) {
		if (!is_int($cell)) {
			throw new \Exception("cell must be an integer!!");
		}
		$objType=getObjectType($contentItem);
		if ($cell == $this->rawCheckbox) {
			if (!($contentItem instanceof \steam_trashbin)) {
				return "<input id=\"{$contentItem->get_id()}_checkbox\" style=\"margin-top:-4px\" type=\"checkbox\" onclick=\"event.stopPropagation(); if(this.checked) { jQuery('#{$contentItem->get_id()}').addClass('listviewer-item-selected') } else { jQuery('#{$contentItem->get_id()}').removeClass('listviewer-item-selected') }\"></input>";
			} else {
				return "";
			}
		} else if ($cell == $this->rawImage) {
				
			return "<img src=\"".PATH_URL."explorer/asset/icons/mimetype/".deriveIcon($contentItem)."\"></img>";
				
		} else if ($cell == $this->rawName) {
			$tipsy = new \Widgets\Tipsy();
			$tipsy->setElementId($contentItem->get_id() . "_" . $this->rawName);
			$tipsy->setHtml("<div style=\"font-weight:bold; width:100px; float:left;\">Besitzer</div> <img style=\"margin: 3px\" align=\"middle\" src=\"".PATH_URL."download/image/"
			. $contentItem->get_creator()->get_attribute(OBJ_ICON)->get_id()."/30/30\"> "
			. $contentItem->get_creator()->get_attribute(USER_FIRSTNAME)." "
			. $contentItem->get_creator()->get_attribute(USER_FULLNAME) . "<br clear=\"all\">"
			. "<div style=\"font-weight:bold; width:100px; float:left;\">zuletzt geändert</div> " . getFormatedDate($contentItem->get_attribute(OBJ_LAST_CHANGED)) . "<br>"
			. "<div style=\"font-weight:bold; width:100px; float:left;\">erstellt</div> " . getFormatedDate($contentItem->get_attribute(OBJ_CREATION_TIME)) . "<br>");


			$url = PATH_URL."explorer/index/".$contentItem->get_id()."/";
			$desc = $contentItem->get_attribute("OBJ_DESC");
			//$name = $objectModel->getReadableName();
			$name = getCleanName($contentItem, 50);
			if (isset($url) && $url != "") {
				return "<a href=\"".$url."\" title=\"$desc\"> " . $name ."</a>" . "<script>" . $tipsy->getHtml() . "</script>";
			} else {
				return $name . "<script>" . $tipsy->getHtml() . "</script>";
			}


		} else if ($cell == $this->rawMarker) {
			$html = "";
			if($objType == "referenceFile"){
				$html .= "<div class=\"marker\">" . \Explorer\Model\Sanction::getMarkerHtml($contentItem->get_link_object()) . "</div>";
			}else{
				$html .= "<div class=\"marker\">" . \Explorer\Model\Sanction::getMarkerHtml($contentItem) . "</div>";
			}
			$html .= "<div class=\"marker\" id=\"{$contentItem->get_id()}_BookmarkMarkerWrapper\">";
			$linkError = false;
			if ($contentItem instanceof \steam_exit) {
				$exitObject = $contentItem->get_exit();
				if ($exitObject instanceof \steam_object) {
					$id = $exitObject->get_id();
				} else {
					$linkError = true;
					$html .= "<div style=\"color:red\">Link defekt</div>";
				}
			} else if ($contentItem instanceof \steam_link) {
				$linkObject = $contentItem->get_link_object();
				if ($linkObject instanceof \steam_object) {
					$id = $linkObject->get_id();
				} else {
					$linkError = true;
					$html .= "<div style=\"color:red\">Link defekt</div>";
				}
			} else {
				$id = $contentItem->get_id();
			}
			if (!$linkError && \Bookmarks\Model\Bookmark::isBookmark($id)) {
				$html .= \Bookmarks\Model\Bookmark::getMarkerHtml($id);
			}
			$html .= "</div>";
			return $html;
		} else if ($cell == $this->rawChangeDate) {
			if($objType == "referenceFile"){
				return getReadableDate($contentItem->get_link_object()->get_attribute("OBJ_LAST_CHANGED"));
			}else{
				return getReadableDate($contentItem->get_attribute("OBJ_LAST_CHANGED"));
			}
		}  else if ($cell == $this->rawSize) {
			return getObjectReadableSize($contentItem);
		} else if ($cell == $this->rawMenu) {
			$popupMenu = new \Widgets\PopupMenu();
			$popupMenu->setData($contentItem);
			$popupMenu->setElementId("listviewer-overlay");
			return $popupMenu;
		}
	}

	public function getNoContentText() {
		return "Dieser Ordner enthält keine Objekte.";
	}

	public function getOnClickHandler($contentItem) {
		if (!($contentItem instanceof \steam_trashbin)) {
			return "jQuery('#{$contentItem->get_id()}').children()[6].children[0].checked = !jQuery('#{$contentItem->get_id()}').children()[6].children[0].checked; widgets_listViewer_selection_toggle({$contentItem->get_id()}, jQuery('#{$contentItem->get_id()}').children()[6].children[0].checked);";
		} else {
			return "";
		}
	}
}

class ColorProvider implements \Widgets\IColorProvider {

	public function getColor($contentItem) {
		$color = $contentItem->get_attribute("OBJ_COLOR_LABEL");
		return ($color === 0) ? "" : $color;
	}


}

class ContentFilter implements \Widgets\IContentFilter {

	public function filterObject($object) {
		if (get_class($object) === "steam_object") {
			return true;
		} else if ($object instanceof \steam_user) {
			return true;
		} else if ($object instanceof \steam_trashbin) {
			return true;
		} else if ($object instanceof \steam_drawing) {
			return true;
		} else if ($object instanceof \steam_calendar) {
			return true;
		} else if ($object instanceof \steam_date) {
			return true;
		} else if ($object instanceof \steam_group) {
			return true;
		} else if ($object instanceof \steam_script) {
			return true;
		} else {
			return false;
		}
	}

}
?>