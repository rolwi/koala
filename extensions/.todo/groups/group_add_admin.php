<?php

require_once( "../etc/koala.conf.php" );
require_once( PATH_LIB . "format_handling.inc.php" );

$portal = lms_portal::get_instance();
$portal->initialize( GUEST_NOT_ALLOWED );
$user = lms_steam::get_current_user();

if ( ! $steam_group = steam_factory::get_object( $GLOBALS[ "STEAM" ]->get_id(), $_REQUEST[ "group" ] ) )
throw new Exception( "Group not found: " . $_REQUEST[ "group" ] );

$iscourse = $steam_group->get_attribute(OBJ_TYPE) === "course";

if ( ! $steam_group instanceof steam_group )
				throw new Exception( "Is not a group: " . $_REQUEST[ "group" ]  );

				$type = (string) $steam_group->get_attribute( "OBJ_TYPE" );
switch( $type )
{
				case ( "course" ):
					$group = new koala_group_course( $steam_group );
					$backlink = PATH_URL . SEMESTER_URL . "/" . $group->get_semester()->get_name() . "/" . $group->get_name();
					break;
				default:
					$group = new koala_group_default( $steam_group );
					$backlink = PATH_URL . "groups/" . $group->get_id() . "/members/";
					break;
}

if ( ! $group->is_admin( $user ) )
{
				throw new Exception( "No admin of " . $group->get_groupname() . ": " . $user->get_name() );
}

if ( isset($_REQUEST[ "add" ]) && count( $_REQUEST[ "add" ] ) > 0 )
{
				$login = key( $_REQUEST[ "add" ] );
				$new_member = steam_factory::username_to_object( $GLOBALS[ "STEAM" ]->get_id(), $login );
				if ( $group->add_admin( $new_member ) )
				{
								//$group_name = $group->get_attribute("OBJ_DESC");
								//if ( !is_string($group_name) || empty($group_name) )
								$group_name = $group->get_display_name();
								$message = str_replace( "%NAME", $new_member->get_attribute( "USER_FIRSTNAME" ) . " " . $new_member->get_attribute( "USER_FULLNAME" ), gettext( "Dear %NAME," ) ). "\n\n";
								$message .= str_replace( "%GROUP", h($group_name), gettext( "You were added to '%GROUP' as a new admin." ) ) . "\n\n";
								$message .= gettext( "This is an automatically generated message." ) . " " . gettext( "If you haven't been informed about this membership in advance, please contact the sender of this message." ) . "\n\n" . str_replace( "%GROUP", "<a href=\"" . $backlink . "\">" . h($group_name). "</a>", gettext( "See '%GROUP' for further information." ));
                                lms_steam::mail($new_member, $user, PLATFORM_NAME . ": " . str_replace( "%GROUP", h($group_name), gettext( "You were added to '%GROUP' as a new admin" ) ), $message);

								$cache = get_cache_function( $new_member->get_name(), 60 );
								$cache->drop( "lms_steam::user_get_groups", $new_member->get_name(), TRUE );
								$cache->drop( "lms_steam::user_get_groups", $new_member->get_name(), FALSE );
								$cache->drop( "lms_steam::user_get_groups", $new_member->get_name() );
								$_SESSION[ "confirmation" ] = str_replace( "%NAME", $new_member->get_attribute( "USER_FIRSTNAME" ) . " " . $new_member->get_attribute( "USER_FULLNAME" ), gettext( "%NAME successfully defined as admin." ) );

								//header( "Location: " . $backlink );
								//exit;
				}
}

 

if ($iscourse) $sinfo = gettext( "You can add a search result as staff member to '<b>%GROUP</b>'." );
else $sinfo = gettext( "You can add a search result as admin to '<b>%GROUP</b>'." );
 
$content = new HTML_TEMPLATE_IT();
$content->loadTemplateFile( PATH_TEMPLATES . "search_persons.template.html" );
$content->setVariable( "HEAD_SEARCH", gettext( "Search" ) );
$content->setVariable( "INFO_TEXT", gettext( "Here you can lookup some people." ) . " " . str_replace( "%GROUP", h($group->get_name()), $sinfo ) );
$content->setVariable( "VALUE_PATTERN", $_REQUEST[ "pattern" ] );
$content->setVariable( "LABEL_CHECK_NAME", gettext( "Name" ) );
$content->setVariable( "LABEL_CHECK_LOGIN", gettext( "Email address or login" ) );
$content->setVariable( "LABEL_SEARCH", gettext( "Search" ) );
$content->setVariable( "GROUP_ID", $group->get_id() );
$content->setVariable( "BACKLINK", "<a href=\"$backlink\">" . gettext( "back" ) . "</a>" );
// SEARCH RESULTS
if ( ! empty( $_REQUEST[ "pattern" ] ) )
{
				$cache = get_cache_function( $user->get_name(), 60 );
				$result = $cache->call( "lms_steam::search_user", $_REQUEST[ "pattern" ], $_REQUEST[ "lookin" ] );
				if( $_REQUEST[ "lookin" ] == "name" )
				{
								$content->setVariable( "CHECKED_NAME", 'checked="checked"' );
				}
				else
				{
								$content->setVariable( "CHECKED_LOGIN", 'checked="checked"' );
				}
				// PROCEED RESULT SET
				$html_people = new HTML_TEMPLATE_IT();
				$html_people->loadTemplateFile( PATH_TEMPLATES . "list_users.template.html" );
				$no_people = count( $result );
				if ( $no_people > 0 )
				{
								$start = $portal->set_paginator( $html_people, 10, $no_people, "(" . gettext( "%TOTAL people in result set" ) . ")", "?pattern=" . $_REQUEST[ "pattern" ] . "&lookin=" . $_REQUEST[ "lookin" ] . "&group=" . $_REQUEST["group"] . "&backlink=" . $_REQUEST["backlink"]);
								$end = ( $start + 10 > $no_people ) ? $no_people : $start + 10;
								$html_people->setVariable( "LABEL_CONTACTS", gettext( "Results" ) . " (" . str_replace( array( "%a", "%z", "%s" ), array( $start + 1, $end, $no_people), gettext( "%a-%z out of %s" ) ) . ")" );
								$html_people->setCurrentBlock( "BLOCK_CONTACT_LIST" );
								$html_people->setVariable( "LABEL_NAME_POSITION", gettext( "Name, position" ) );
								$html_people->setVariable( "LABEL_SUBJECT_AREA", gettext( "Subject area" ) );
								$html_people->setVariable( "LABEL_COMMUNICATION", gettext( "Communication" ) );
								$html_people->setVariable( "TH_MANAGE_CONTACT", gettext( "Action" ) );
								for ( $i = $start; $i < $end; $i++ )
								{
												$person = $result[ $i ];
												$html_people->setCurrentBlock( "BLOCK_CONTACT" );
												$html_people->setVariable( "CONTACT_LINK", PATH_URL . "user/" . h($person[ "OBJ_NAME" ]). "/" );
												$icon_link = ( $person[ "OBJ_ICON" ] == 0 ) ? PATH_STYLE . "images/anonymous.jpg" : PATH_URL . "cached/get_document.php?id=" . $person[ "OBJ_ICON" ];
												$html_people->setVariable( "CONTACT_IMAGE", $icon_link );
												$html_people->setVariable( "CONTACT_NAME", h($person[ "USER_FIRSTNAME" ]) . " " . h($person[ "USER_FULLNAME" ]) );
												$html_people->setVariable( "LINK_SEND_MESSAGE", PATH_URL . "messages_write.php?to=" . h($person[ "OBJ_NAME" ]) );
												$html_people->setVariable( "LABEL_MESSAGE", gettext( "Message" ) );
												$html_people->setVariable( "LABEL_SEND", gettext( "Send" ) );
												$html_people->setVariable( "OBJ_DESC", h($person[ "OBJ_DESC" ]) );
												$p = new steam_object( $GLOBALS[ "STEAM" ], $person[ "OBJ_ID" ] );
												if ( ! $group->is_admin( $p ) )
												{
												   $html_people->setVariable( "TD_MANAGE_CONTACT", "<td align=\"center\"><input type=\"submit\"  name=\"add[" . $person[ "OBJ_NAME" ]. "]\" value=\"" . gettext( "Add" ). "\"/></td>" );
												}
												else
												{
												   $html_people->setVariable( "TD_MANAGE_CONTACT", "<td align=\"center\">".gettext( "Already an admin." )."</td>" );
												}
												$html_people->parse( "BLOCK_CONTACT" );
								}
								$html_people->parse( "BLOCK_CONTACT_LIST" );
				}
				else
				{
								$html_people->setVariable( "LABEL_CONTACTS", gettext( "No results." ) );
				}
				$content->setVariable( "HTML_USER_LIST", $html_people->get() );

}
else
{
				$content->setVariable( "CHECKED_NAME", 'checked="checked"' );
}


if ($iscourse) $stitle = gettext("Add staff member");
else $stitle = gettext("Add admin");


$portal->set_page_title( $stitle );
$portal->set_page_main(
								array( array( "link" => $backlink, "name" => $group->get_display_name() ), array( "link" => "", "name" => gettext( $stitle ) ) ),
								$content->get(),
								""
								);
$portal->show_html()

				?>
