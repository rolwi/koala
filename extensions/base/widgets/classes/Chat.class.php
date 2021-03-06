<?php
namespace Widgets;

class Chat extends Widget {
	private $label;
	private $data;
	
	public function setData($data) {
		$this->data = $data;
	}
	
	public function getHtml() {
		$portal = \lms_portal::get_instance();
		$annotations = $this->data->get_annotations();
		$annotations = array_reverse($annotations);
		$lastAnnotation = 0;
		foreach ($annotations as $annotation) {
			$this->getContent()->setCurrentBlock("BLOCK_CHAT");
			if ($lastAnnotation === 0) {
				$lastAnnotation = $annotation->get_attribute("OBJ_CREATION_TIME");
				$this->getContent()->setCurrentBlock("BLOCK_STATUS");
				$this->getContent()->setVariable("STATUS_MESSAGE", /*"Diskussion mit " . $portal->get_user()->get_forename() . " " . $portal->get_user()->get_surname() . ".<br>" . */getReadableDate($lastAnnotation));
				$this->getContent()->parse("BLOCK_STATUS");
			} else {
				$tmp = $lastAnnotation;
				$lastAnnotation = $annotation->get_attribute("OBJ_CREATION_TIME");
				if ($lastAnnotation - $tmp > 600) {
					$this->getContent()->setCurrentBlock("BLOCK_STATUS");
					$this->getContent()->setVariable("STATUS_MESSAGE", getReadableDate($lastAnnotation));
					$this->getContent()->parse("BLOCK_STATUS");
				}
			}
			$creator = $annotation->get_creator();
                        if ($creator->get_name() === $portal->get_user()->get_login()) {
                            $this->getContent()->setCurrentBlock("BLOCK_OUTGOING");
                            $this->getContent()->setVariable("OUTGOING_MESSAGE", $this->replaceEmoticon($annotation->get_content()));
                            $this->getContent()->setVariable("OUTGOING_IMG", \lms_user::get_user_image_url(32, 32));
                            $this->getContent()->setVariable("OUTGOING_TITLE", $portal->get_user()->get_forename() . " " . $portal->get_user()->get_surname());
                            $this->getContent()->parse("BLOCK_OUTGOING");
                            $this->getContent()->parse("BLOCK_CHAT");
                        } else {
                            $this->getContent()->setCurrentBlock("BLOCK_INCOMING");
                            $this->getContent()->setVariable("INCOMING_MESSAGE", $this->replaceEmoticon($annotation->get_content()));
                            $this->getContent()->setVariable("INCOMING_IMG", \lms_user::get_user_image_url(32, 32, $creator->get_attribute("OBJ_ICON")));
                            $this->getContent()->setVariable("INCOMING_TITLE", $creator->get_attribute("USER_FIRSTNAME") . " " . $creator->get_attribute("USER_FULLNAME"));
                            $this->getContent()->parse("BLOCK_INCOMING");
                            $this->getContent()->parse("BLOCK_CHAT");
                        }
		}
		return $this->getContent()->get();
	}
        
        private function replaceEmoticon($message) {
            //$message = str_replace(array(":-)", ":)", "=)", ":]", ":>", ":c)", "x)", ":o)"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-smile.png\">", $message);
            //$message = str_replace(array("(-:", "(:", "(=", "[:", "<:", "c:", "(x", "(o:", "c\",)"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-smile.png\">", $message);
            //$message = str_replace(array(":-(", ":(", "=(", ":[", ":<", ":/", "x(", ":o(", ":C"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-sad.png\">", $message);
            $message = str_replace(array(":-)"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-smile.png\">", $message);
            $message = str_replace(array(":-("), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-sad.png\">", $message);
            $message = str_replace(array(":'("), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-crying.png\">", $message);
            $message = str_replace(array(";-)"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-wink.png\">", $message);
            $message = str_replace(array(":-D"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-grin.png\">", $message);
            $message = str_replace(array(":-0"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-surprise.png\">", $message);
            $message = str_replace(array("*angle*"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-angel.png\">", $message);
            $message = str_replace(array("*devil*"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-devilish.png\">", $message);
            $message = str_replace(array("8-)"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-glasses.png\">", $message);
            $message = str_replace(array(":-X"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-kiss.png\">", $message);
            $message = str_replace(array("*ape*"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-monkey.png\">", $message);
            $message = str_replace(array(":-|"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-plain.png\">", $message);
            $message = str_replace(array(":-))"), "<img src=\"". PATH_URL . "widgets/asset/emotes/face-smile-big.png\">", $message);
            $message = str_replace(array("*richtig*"), "<img src=\"". PATH_URL . "widgets/asset/emotes/right.png\">", $message);
            $message = str_replace(array("*falsch*"), "<img src=\"". PATH_URL . "widgets/asset/emotes/wrong.png\">", $message);
            return $message;
        }
	
}
?>