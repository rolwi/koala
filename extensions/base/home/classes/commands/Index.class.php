<?php
namespace Home\Commands;
class Index extends \AbstractCommand implements \IFrameCommand {
	
	public function validateData(\IRequestObject $requestObject) {
		return true;
	}
	
	public function processData(\IRequestObject $requestObject) {
		
	}
	
	public function frameResponse(\FrameResponseObject $frameResponseObject) {
		$portal = \lms_portal::get_instance();
		$user = \lms_steam::get_current_user();
		$frameResponseObject->setTitle(gettext("Your Desktop"));

		// Cache for 7 Minutes
		$cache = get_cache_function($user->get_name(), 420);
		$feeds = $cache->call("koala_user::get_news_feeds_static", 0, 10, FALSE, $user);

		$home = \Home::getInstance();
		//$home->addJS();
		$content = $home->loadTemplate("home.template.html");
		
		$infobar = new \Widgets\InfoBar();
		$infobar->setHeadline(gettext("Hallo") . " " . $portal->get_user()->get_forename() . " " . $portal->get_user()->get_surname() . "!");
		$content->setVariable("INFOBAR", $infobar->getHtml());
		
		$captionImage = new \Widgets\CaptionImage();
		$captionImage->setLink(PATH_URL . "user/index/" . $user->get_name() . "/");
		$captionImage->setLinkText(gettext("To your profile"));
		$captionImage->setImageSrc(\lms_user::get_user_image_url(140,185));
		$captionImage->setImageAlt(gettext("Profile Image"));
		$captionImage->setImageTitle(gettext("Complete your Profile"));
		$content->setVariable("PROFILEIMAGE", $captionImage->getHtml());
		
		$rawHtml = new \Widgets\RawHtml();
		$rawHtml->addWidget($infobar);
		$rawHtml->addWidget($captionImage);
		
		$homeExtensions = \ExtensionMaster::getInstance()->getExtensionByType("IHomeExtension");
		foreach ($homeExtensions as $homeExtension) {
			$content->setCurrentBlock("HOME_EXTENSION");
			$widget = $homeExtension->getWidget();
			$rawHtml->addWidget($widget);
			$content->setVariable("HOME_EXTENSION_CONTENT", $widget->getHtml());
			$content->parse("HOME_EXTENSION");
		}
		
		$rawHtml->setHtml($content->get());
		$frameResponseObject->addWidget($rawHtml);
		return $frameResponseObject;
	}
	
	
}
?>