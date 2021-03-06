<?php
$wiki_html_handler = new lms_wiki( $wiki_container );
$wiki_html_handler->set_admin_menu( "mediathek", $wiki_container );

$content = new HTML_TEMPLATE_IT();
$content->loadTemplateFile( PATH_TEMPLATES . "wiki_mediathek.template.html" );

// get images
$inventory = $wiki_container->get_inventory();
if ( !is_array( $inventory ) ) $inventory = array();

if ( sizeof( $inventory ) > 0 )
{
	steam_factory::load_attributes( $GLOBALS["STEAM"]->get_id(), $inventory , array( OBJ_NAME, OBJ_DESC, DOC_MIME_TYPE ) );
	$images = array();

	foreach( $inventory as $object )
	{
    	$mime = strtolower($object->get_attribute(DOC_MIME_TYPE));
    	if ( $mime === "image/jpg" || $mime === "image/jpeg" || $mime === "image/gif" || $mime === "image/png" ) $images[] = $object;
	}
	
	foreach( $images as $image )
	{
		$actions = '<a href="' . PATH_URL . 'doc/' . $image->get_id() . '/">' . gettext("show properties") . '</a><br>';
		$actions .= '<a href="' . PATH_URL . 'doc/' . $image->get_id() . '/edit/">' . gettext("edit properties") . '</a><br>';
		$actions .= '<a href="' . PATH_URL . 'doc/' . $image->get_id() . '/deleteImage/" onclick="return confirmDeletion();">' . gettext("delete image") . '</a>';
		
		$imageData = imagecreatefromstring( $image->get_content() );

		$width = $newWidth = imagesx( $imageData );
		$height = $newHeight = imagesy( $imageData );

		if ( $width > 160 )
		{
			$newHeight = (int) ( $height * 160 / $width );
			$newWidth = 160;
		}
		
		if ( $newHeight > 80 )
		{
			$newWidth = (int) ( $newWidth * 80 / $newHeight );
			$newHeight = 80;
		}
		
		$content->setCurrentBlock("BLOCK_IMAGE");
		    $content->setVariable("IMAGE_NAME", $image->get_name());
    	    $content->setVariable("IMAGE_ID", $image->get_id());
			$content->setVariable("IMAGE_DESCRIPTION", $image->get_attribute('OBJ_DESC'));
		    $content->setVariable("IMAGE_LINK", PATH_URL . "get_document.php?id=" . $image->get_id() . "&width=" . $newWidth . "&height=" . $newHeight);
		    $content->setVariable("PREVIEW_LINK", "javascript:showBox(" . $image->get_id() . "," . $width . "," . $height . ");");
		    $content->setVariable("IMAGE_ACTIONS", $actions);	    
		$content->parse("BLOCK_IMAGE");
	}
}

$question = gettext( "Do you really want to delete this image?" );
$note = gettext("NOTE: All wiki-entries containing this image have to be updated manually!" );
$content->setVariable( "QUESTION", $question );
$content->setVariable( "NOTE", $note );
$content->setVariable( "LABEL_CLOSE", gettext( "close" ) );
$content->setVariable( "BACK_LINK", PATH_URL . "wiki/" . $wiki_container->get_id() . "/" );
$content->setVariable( "BACK_LABEL", gettext( "back" ) );

$wiki_html_handler->set_main_html( $content->get() );

// breadcrumbs
$rootlink = lms_steam::get_link_to_root( $wiki_container );
(WIKI_FULL_HEADLINE) ? 
$headline = array(
				$rootlink[0],
				$rootlink[1],
				array( "link" => $rootlink[1]["link"] . "communication/", "name" => gettext("Communication")),
				array( "name" =>  h($wiki_container->get_name()), "link" => PATH_URL . "wiki/" . $wiki_container->get_id() . "/"),
				array( "link" => "", "name" => gettext("Mediathek") )
			):
$headline = array(
				array( "name" =>  h($wiki_container->get_name()), "link" => PATH_URL . "wiki/" . $wiki_container->get_id() . "/"), 
				array( "link" => "", "name" => gettext("Mediathek") )
			);

$portal->set_page_main(
		$headline,
		$wiki_html_handler->get_html()
		);
$portal->show_html();
?>