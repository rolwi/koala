<?php
namespace Wiki\Commands;

class Index extends \AbstractCommand implements \IFrameCommand {
	
	private $params;
	
	public function validateData(\IRequestObject $requestObject) {
		$this->params = $requestObject->getParams();
		
		if (isset($this->params[0])) {
			return true;
		} 
		else {
			return false;
		}
	}
	
	public function processData(\IRequestObject $requestObject) {
		$this->params = $requestObject->getParams();
	}
	
	public function frameResponse(\FrameResponseObject $frameResponseObject) {
		
		$wiki_id = $this->params[0];
		
		//$portal = \lms_portal::get_instance();
		$user = \lms_steam::get_current_user();
		$myExtension = \Wiki::getInstance();
		// Disable caching
		// TODO: Work on cache handling. An enabled cache leads to bugs
		// if used with the wiki.
		\CacheSettings::disable_caching();		
		
		if ( ! $wiki_container = \steam_factory::get_object( $GLOBALS[ "STEAM" ]->get_id(), $wiki_id))
		{
			include( "bad_link.php" );
			exit;
		}
		if ( ! $wiki_container instanceof \steam_container )
		{
			$wiki_doc = $wiki_container;
			$wiki_container = $wiki_doc->get_environment();
			if ( $wiki_doc->get_attribute( DOC_MIME_TYPE ) != "text/wiki" )
			{
				include( "bad_link.php" );
				exit;
			}
		}
		
		$startpage = $wiki_container->get_attribute("OBJ_WIKI_STARTPAGE");

		if ( !$startpage || $startpage == "glossary" )
		{
			define( "OBJ_ID", $wiki_container->get_id() );
			
			$wiki_html_handler = new \lms_wiki( $wiki_container );
			$wiki_html_handler->set_admin_menu( "index", $wiki_container );
			
			$grp = $wiki_container->get_environment()->get_creator();
			if ($grp->get_name() == "learners" && $grp->get_attribute(OBJ_TYPE) == "course_learners") {
			  $grp = $grp->get_parent_group();
			}
			$content = $myExtension->loadTemplate("wiki_entries.template.html");
			
			$cache = get_cache_function( $wiki_container->get_id(), 600 );
			$wiki_entries = $cache->call( "lms_wiki::get_items", $wiki_container->get_id() );
			
			$recently_changed = new \LinkedList( 5 );
			$most_discussed   = new \LinkedList( 5 );
			$latest_comments  = new \LinkedList( 5 );
			
			$no_wiki_entries = count( $wiki_entries );
			
			if ( $no_wiki_entries > 0 )
			{
				$first_char = "";
				for( $i = 0; $i < $no_wiki_entries; $i++ )
				{
					$this_char = substr( strtoupper( $wiki_entries[ $i ][ "OBJ_NAME" ] ), 0, 1  );
					if ( $this_char > $first_char )
					{
						$first_char = $this_char;
						if ( $i > 1 )
						{
							$content->parse( "BLOCK_CHARACTER" );
						}
						$content->setCurrentBlock( "BLOCK_CHARACTER" );
						$content->setVariable( "FIRST_CHAR", h($this_char) );
					}
					$char_articles = array();
					while ( $i < $no_wiki_entries && $this_char == substr( strtoupper( $wiki_entries[ $i ][ "OBJ_NAME" ] ), 0, 1 ) )
					{
						$char_articles[] = $wiki_entries[ $i ];
						if ( $recently_changed->can_be_added( $wiki_entries[ $i ][ "DOC_LAST_MODIFIED" ] ) )
						{
							$recently_changed->add_element(
							$wiki_entries[ $i ][ "DOC_LAST_MODIFIED" ],
							$wiki_entries[ $i ]
							);
						}
						if ( isset($wiki_entries[ $i ][ "COMMENTS_NO" ]) && $most_discussed->can_be_added( $wiki_entries[ $i ][ "COMMENTS_NO" ] ) && $wiki_entries[ $i ][ "COMMENTS_NO" ] > 1 )
						{
							$most_discussed->add_element(
							$wiki_entries[ $i ][ "COMMENTS_NO" ],
							$wiki_entries[ $i ]
							);
						}
						if ( isset($wiki_entries[ $i ][ "COMMENTS_LAST" ] ) && $latest_comments->can_be_added( $wiki_entries[ $i ][ "COMMENTS_LAST" ] ) && $wiki_entries[ $i ][ "COMMENTS_LAST" ] > 0 )
						{
							$latest_comments->add_element(
							$wiki_entries[ $i ][ "COMMENTS_LAST" ],
							$wiki_entries[ $i ]
							);
						}
						$i++;
					}
					$i--;
					$no_articles_in_first_row = ceil( count( $char_articles ) / 2 );
			
					$content->setCurrentBlock( "BLOCK_COLUMN" );
					for ( $c = 0; $c < $no_articles_in_first_row; $c++ )
					{
						$content->setCurrentBlock( "BLOCK_ARTICLE" );
						$content->setVariable( "ARTICLE_LINK", PATH_URL . "wiki/viewEntry/" . $char_articles[ $c ][ "OBJ_ID" ] . "/" );
						$content->setVariable( "ARTICLE_NAME", str_replace( ".wiki", "", h($char_articles[ $c ][ "OBJ_NAME" ] )) );
						$content->parse( "BLOCK_ARTICLE" );
					}
					$content->parse( "BLOCK_COLUMN" );
			
					$content->setCurrentBlock( "BLOCK_COLUMN" );
					for ( $c = $no_articles_in_first_row; $c < count( $char_articles ); $c++ )
					{
						$content->setCurrentBlock( "BLOCK_ARTICLE" );
						// TODO: Passt der Pfad?
						$content->setVariable( "ARTICLE_LINK", PATH_URL . "wiki/" . $char_articles[ $c ][ "OBJ_ID" ] . "/" );
						$content->setVariable( "ARTICLE_NAME", str_replace( ".wiki", "", h($char_articles[ $c ][ "OBJ_NAME" ] )) );
						$content->parse( "BLOCK_ARTICLE" );
					}
					$content->parse( "BLOCK_COLUMN" );
					$content->parse( "BLOCK_CHARACTER" );
				}
			
				foreach( $wiki_entries as $entry )
				{
					$content->setCurrentBlock( "BLOCK_ARTICLE" );
					$content->setVariable( "VALUE_WIKI_ENTRY", h($entry[ "OBJ_NAME" ]) );
					// TODO: Passt der Pfad?
					$content->setVariable( "LINK_WIKI_ENTRY", PATH_URL . "wiki/" . $wiki_container->get_id() . "/" . h($entry[ "OBJ_NAME" ]) );
					$content->setVariable( "LABEL_LAST_MODIFICATION", gettext( "last edited" ) );
					$content->setVariable( "VALUE_POSTED_BY", $entry[ "DOC_USER_MODIFIED" ] );
					// TODO: Passt der Pfad?
					$content->setVariable( "POST_PERMALINK", PATH_URL . "wiki/" . $entry[ "OBJ_ID" ] . "/" );
					$content->setVariable( "VALUE_DATE_TIME", strftime( "%x %X", $entry[ "OBJ_CREATION_TIME" ] ) );
					// TODO: Passt der Pfad?
					$content->setVariable( "POST_PERMALINK_LABEL", gettext( "permalink" ) );
					$content->parse( "BLOCK_ARTICLE" );
				}
			}
			
			//(WIKI_RSS) ? $portal->set_rss_feed(PATH_URL . "services/feeds/wiki_public.php?id=" . OBJ_ID , gettext("Feed"), gettext("Subscribe to this forum's Newsfeed")) : "";
			$wiki_html_handler->set_main_html( $content->get());
			$rootlink = \lms_steam::get_link_to_root( $wiki_container );
			//(WIKI_FULL_HEADLINE) ? 
			$headline = array( $rootlink[0], $rootlink[1], array("link" => $rootlink[1]["link"] . "communication/", "name" => gettext("Communication")), array( "link" => "", "name" => h($wiki_container->get_name() )) );  
			//$headline = array(array( "link" => "", "name" => h($wiki_container->get_name())));
			
			
			$frameResponseObject->setTitle("Wiki");
			$frameResponseObject->setHeadline($headline);
			$rawHtml = new \Widgets\RawHtml();
			$rawHtml->setHtml($content->get());
			$frameResponseObject->addWidget($rawHtml);
			
			return $frameResponseObject;
		}
		else
		{	
			// TODO: Passt der Pfad?
			header("Location: " . PATH_URL . "wiki/" . $wiki_container->get_id() . "/" . $startpage . ".wiki");
		}	
	}
}

?>