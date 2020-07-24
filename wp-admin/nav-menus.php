<?php
/**
 * WordPress Administration for Navigation s
 * Interface functions
 *
 * @version 2.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Load all the nav  interface functions.
require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

if ( ! current_theme_supports( 's' ) && ! current_theme_supports( 'widgets' ) ) {
	wp_die( __( 'Your theme does not support navigation s or widgets.' ) );
}

// Permissions check.
if ( ! current_user_can( 'edit_theme_options' ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to edit theme options on this site.' ) . '</p>',
		403
	);
}

wp_enqueue_script( 'nav-menu' );

if ( wp_is_mobile() ) {
	wp_enqueue_script( 'jquery-touch-punch' );
}

// Container for any messages displayed to the user.
$messages = array();

// Container that stores the name of the active .
$nav_menu_selected_title = '';

// The  id of the current  being edited.
$nav_menu_selected_id = isset( $_REQUEST[''] ) ? (int) $_REQUEST[''] : 0;

// Get existing  locations assignments.
$locations      = get_registered_nav_menus();
$_locations = get_nav_menu_locations();
$num_locations  = count( array_keys( $locations ) );

// Allowed actions: add, update, delete.
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

/*
 * If a JSON blob of navigation  data is found, expand it and inject it
 * into `$_POST` to avoid PHP `max_input_vars` limitations. See #14134.
 */
_wp_expand_nav_menu_post_data();

switch ( $action ) {
	case 'add--item':
		check_admin_referer( 'add-_item', '-settings-column-nonce' );
		if ( isset( $_REQUEST['nav-menu-locations'] ) ) {
			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_REQUEST['-locations'] ) );
		} elseif ( isset( $_REQUEST['-item'] ) ) {
			wp_save_nav_menu_items( $nav_menu_selected_id, $_REQUEST['-item'] );
		}
		break;
	case 'move-down--item':
		// Moving down a  item is the same as moving up the next in order.
		check_admin_referer( 'move-_item' );
		$_item_id = isset( $_REQUEST['-item'] ) ? (int) $_REQUEST['-item'] : 0;
		if ( is_nav_menu_item( $_item_id ) ) {
			$s = isset( $_REQUEST[''] ) ? array( (int) $_REQUEST[''] ) : wp_get_object_terms( $_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $s ) && ! empty( $s[0] ) ) {
				$_id            = (int) $s[0];
				$ordered__items = wp_get_nav_menu_items( $_id );
				$_item_data     = (array) wp_setup_nav_menu_item( get_post( $_item_id ) );

				// Set up the data we need in one pass through the array of  items.
				$dbids_to_orders = array();
				$orders_to_dbids = array();
				foreach ( (array) $ordered__items as $ordered__item_object ) {
					if ( isset( $ordered__item_object->ID ) ) {
						if ( isset( $ordered__item_object->_order ) ) {
							$dbids_to_orders[ $ordered__item_object->ID ]         = $ordered__item_object->_order;
							$orders_to_dbids[ $ordered__item_object->_order ] = $ordered__item_object->ID;
						}
					}
				}

				// Get next in order.
				if (
					isset( $orders_to_dbids[ $dbids_to_orders[ $_item_id ] + 1 ] )
				) {
					$next_item_id   = $orders_to_dbids[ $dbids_to_orders[ $_item_id ] + 1 ];
					$next_item_data = (array) wp_setup_nav_menu_item( get_post( $next_item_id ) );

					// If not siblings of same parent, bubble  item up but keep order.
					if (
						! empty( $_item_data['_item_parent'] ) &&
						(
							empty( $next_item_data['_item_parent'] ) ||
							$next_item_data['_item_parent'] != $_item_data['_item_parent']
						)
					) {

						$parent_db_id = in_array( $_item_data['_item_parent'], $orders_to_dbids ) ? (int) $_item_data['_item_parent'] : 0;

						$parent_object = wp_setup_nav_menu_item( get_post( $parent_db_id ) );

						if ( ! is_wp_error( $parent_object ) ) {
							$parent_data                        = (array) $parent_object;
							$_item_data['_item_parent'] = $parent_data['_item_parent'];
							update_post_meta( $_item_data['ID'], '__item__item_parent', (int) $_item_data['_item_parent'] );

						}

						// Make  item a child of its next sibling.
					} else {
						$next_item_data['_order'] = $next_item_data['_order'] - 1;
						$_item_data['_order'] = $_item_data['_order'] + 1;

						$_item_data['_item_parent'] = $next_item_data['ID'];
						update_post_meta( $_item_data['ID'], '__item__item_parent', (int) $_item_data['_item_parent'] );

						wp_update_post( $_item_data );
						wp_update_post( $next_item_data );
					}

					// The item is last but still has a parent, so bubble up.
				} elseif (
					! empty( $_item_data['_item_parent'] ) &&
					in_array( $_item_data['_item_parent'], $orders_to_dbids )
				) {
					$_item_data['_item_parent'] = (int) get_post_meta( $_item_data['_item_parent'], '__item__item_parent', true );
					update_post_meta( $_item_data['ID'], '__item__item_parent', (int) $_item_data['_item_parent'] );
				}
			}
		}

		break;
	case 'move-up--item':
		check_admin_referer( 'move-_item' );
		$_item_id = isset( $_REQUEST['-item'] ) ? (int) $_REQUEST['-item'] : 0;
		if ( is_nav_menu_item( $_item_id ) ) {
			$s = isset( $_REQUEST[''] ) ? array( (int) $_REQUEST[''] ) : wp_get_object_terms( $_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $s ) && ! empty( $s[0] ) ) {
				$_id            = (int) $s[0];
				$ordered__items = wp_get_nav_menu_items( $_id );
				$_item_data     = (array) wp_setup_nav_menu_item( get_post( $_item_id ) );

				// Set up the data we need in one pass through the array of  items.
				$dbids_to_orders = array();
				$orders_to_dbids = array();
				foreach ( (array) $ordered__items as $ordered__item_object ) {
					if ( isset( $ordered__item_object->ID ) ) {
						if ( isset( $ordered__item_object->_order ) ) {
							$dbids_to_orders[ $ordered__item_object->ID ]         = $ordered__item_object->_order;
							$orders_to_dbids[ $ordered__item_object->_order ] = $ordered__item_object->ID;
						}
					}
				}

				// If this  item is not first.
				if ( ! empty( $dbids_to_orders[ $_item_id ] ) && ! empty( $orders_to_dbids[ $dbids_to_orders[ $_item_id ] - 1 ] ) ) {

					// If this  item is a child of the previous.
					if (
						! empty( $_item_data['_item_parent'] ) &&
						in_array( $_item_data['_item_parent'], array_keys( $dbids_to_orders ) ) &&
						isset( $orders_to_dbids[ $dbids_to_orders[ $_item_id ] - 1 ] ) &&
						( $_item_data['_item_parent'] == $orders_to_dbids[ $dbids_to_orders[ $_item_id ] - 1 ] )
					) {
						$parent_db_id  = in_array( $_item_data['_item_parent'], $orders_to_dbids ) ? (int) $_item_data['_item_parent'] : 0;
						$parent_object = wp_setup_nav_menu_item( get_post( $parent_db_id ) );

						if ( ! is_wp_error( $parent_object ) ) {
							$parent_data = (array) $parent_object;

							/*
							 * If there is something before the parent and parent a child of it,
							 * make  item a child also of it.
							 */
							if (
								! empty( $dbids_to_orders[ $parent_db_id ] ) &&
								! empty( $orders_to_dbids[ $dbids_to_orders[ $parent_db_id ] - 1 ] ) &&
								! empty( $parent_data['_item_parent'] )
							) {
								$_item_data['_item_parent'] = $parent_data['_item_parent'];

								/*
								* Else if there is something before parent and parent not a child of it,
								* make  item a child of that something's parent
								*/
							} elseif (
								! empty( $dbids_to_orders[ $parent_db_id ] ) &&
								! empty( $orders_to_dbids[ $dbids_to_orders[ $parent_db_id ] - 1 ] )
							) {
								$_possible_parent_id = (int) get_post_meta( $orders_to_dbids[ $dbids_to_orders[ $parent_db_id ] - 1 ], '__item__item_parent', true );
								if ( in_array( $_possible_parent_id, array_keys( $dbids_to_orders ) ) ) {
									$_item_data['_item_parent'] = $_possible_parent_id;
								} else {
									$_item_data['_item_parent'] = 0;
								}

								// Else there isn't something before the parent.
							} else {
								$_item_data['_item_parent'] = 0;
							}

							// Set former parent's [_order] to that of -item's.
							$parent_data['_order'] = $parent_data['_order'] + 1;

							// Set -item's [_order] to that of former parent.
							$_item_data['_order'] = $_item_data['_order'] - 1;

							// Save changes.
							update_post_meta( $_item_data['ID'], '__item__item_parent', (int) $_item_data['_item_parent'] );
							wp_update_post( $_item_data );
							wp_update_post( $parent_data );
						}

						// Else this  item is not a child of the previous.
					} elseif (
						empty( $_item_data['_order'] ) ||
						empty( $_item_data['_item_parent'] ) ||
						! in_array( $_item_data['_item_parent'], array_keys( $dbids_to_orders ) ) ||
						empty( $orders_to_dbids[ $dbids_to_orders[ $_item_id ] - 1 ] ) ||
						$orders_to_dbids[ $dbids_to_orders[ $_item_id ] - 1 ] != $_item_data['_item_parent']
					) {
						// Just make it a child of the previous; keep the order.
						$_item_data['_item_parent'] = (int) $orders_to_dbids[ $dbids_to_orders[ $_item_id ] - 1 ];
						update_post_meta( $_item_data['ID'], '__item__item_parent', (int) $_item_data['_item_parent'] );
						wp_update_post( $_item_data );
					}
				}
			}
		}
		break;

	case 'delete--item':
		$_item_id = (int) $_REQUEST['-item'];

		check_admin_referer( 'delete-_item_' . $_item_id );

		if ( is_nav_menu_item( $_item_id ) && wp_delete_post( $_item_id, true ) ) {
			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'The  item has been successfully deleted.' ) . '</p></div>';
		}
		break;

	case 'delete':
		check_admin_referer( 'delete-nav_menu-' . $nav_menu_selected_id );
		if ( is_nav_menu( $nav_menu_selected_id ) ) {
			$deletion = wp_delete_nav_menu( $nav_menu_selected_id );
		} else {
			// Reset the selected .
			$nav_menu_selected_id = 0;
			unset( $_REQUEST[''] );
		}

		if ( ! isset( $deletion ) ) {
			break;
		}

		if ( is_wp_error( $deletion ) ) {
			$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $deletion->get_error_message() . '</p></div>';
		} else {
			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'The  has been successfully deleted.' ) . '</p></div>';
		}
		break;

	case 'delete_s':
		check_admin_referer( 'nav_menus_bulk_actions' );
		foreach ( $_REQUEST['delete_s'] as $_id_to_delete ) {
			if ( ! is_nav_menu( $_id_to_delete ) ) {
				continue;
			}

			$deletion = wp_delete_nav_menu( $_id_to_delete );
			if ( is_wp_error( $deletion ) ) {
				$messages[]     = '<div id="message" class="error notice is-dismissible"><p>' . $deletion->get_error_message() . '</p></div>';
				$deletion_error = true;
			}
		}

		if ( empty( $deletion_error ) ) {
			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Selected s have been successfully deleted.' ) . '</p></div>';
		}
		break;

	case 'update':
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Remove  locations that have been unchecked.
		foreach ( $locations as $location => $description ) {
			if ( ( empty( $_POST['-locations'] ) || empty( $_POST['-locations'][ $location ] ) ) && isset( $_locations[ $location ] ) && $_locations[ $location ] == $nav_menu_selected_id ) {
				unset( $_locations[ $location ] );
			}
		}

		// Merge new and existing  locations if any new ones are set.
		if ( isset( $_POST['-locations'] ) ) {
			$new__locations = array_map( 'absint', $_POST['-locations'] );
			$_locations     = array_merge( $_locations, $new__locations );
		}

		// Set  locations.
		set_theme_mod( 'nav_menu_locations', $_locations );

		// Add .
		if ( 0 == $nav_menu_selected_id ) {
			$new__title = trim( esc_html( $_POST['-name'] ) );

			if ( $new__title ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array( '-name' => $new__title ) );

				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$__object            = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_id    = $_nav_menu_selected_id;
					$nav_menu_selected_title = $__object->name;
					if ( isset( $_REQUEST['-item'] ) ) {
						wp_save_nav_menu_items( $nav_menu_selected_id, absint( $_REQUEST['-item'] ) );
					}
					if ( isset( $_REQUEST['zero--state'] ) ) {
						// If there are  items, add them.
						wp_nav_menu_update__items( $nav_menu_selected_id, $nav_menu_selected_title );
						// Auto-save nav_menu_locations.
						$locations = get_nav_menu_locations();
						foreach ( $locations as $location => $_id ) {
								$locations[ $location ] = $nav_menu_selected_id;
								break; // There should only be 1.
						}
						set_theme_mod( 'nav_menu_locations', $locations );
					}
					if ( isset( $_REQUEST['use-location'] ) ) {
						$locations      = get_registered_nav_menus();
						$_locations = get_nav_menu_locations();
						if ( isset( $locations[ $_REQUEST['use-location'] ] ) ) {
							$_locations[ $_REQUEST['use-location'] ] = $nav_menu_selected_id;
						}
						set_theme_mod( 'nav_menu_locations', $_locations );
					}

					// $messages[] = '<div id="message" class="updated"><p>' . sprintf( __( '<strong>%s</strong> has been created.' ), $nav_menu_selected_title ) . '</p></div>';
					wp_redirect( admin_url( 'nav-menus.php?=' . $_nav_menu_selected_id ) );
					exit();
				}
			} else {
				$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid  name.' ) . '</p></div>';
			}

			// Update existing .
		} else {

			$__object = wp_get_nav_menu_object( $nav_menu_selected_id );

			$_title = trim( esc_html( $_POST['-name'] ) );
			if ( ! $_title ) {
				$messages[] = '<div id="message" class="error notice is-dismissible"><p>' . __( 'Please enter a valid  name.' ) . '</p></div>';
				$_title = $__object->name;
			}

			if ( ! is_wp_error( $__object ) ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( $nav_menu_selected_id, array( '-name' => $_title ) );
				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$__object = $_nav_menu_selected_id;
					$messages[]   = '<div id="message" class="error notice is-dismissible"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$__object            = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_title = $__object->name;
				}
			}

			// Update  items.
			if ( ! is_wp_error( $__object ) ) {
				$messages = array_merge( $messages, wp_nav_menu_update__items( $_nav_menu_selected_id, $nav_menu_selected_title ) );

				// If the  ID changed, redirect to the new URL.
				if ( $nav_menu_selected_id != $_nav_menu_selected_id ) {
					wp_redirect( admin_url( 'nav-menus.php?=' . intval( $_nav_menu_selected_id ) ) );
					exit();
				}
			}
		}
		break;
	case 'locations':
		if ( ! $num_locations ) {
			wp_redirect( admin_url( 'nav-menus.php' ) );
			exit();
		}

		add_filter( 'screen_options_show_screen', '__return_false' );

		if ( isset( $_POST['-locations'] ) ) {
			check_admin_referer( 'save--locations' );

			$new__locations = array_map( 'absint', $_POST['-locations'] );
			$_locations     = array_merge( $_locations, $new__locations );
			// Set  locations.
			set_theme_mod( 'nav_menu_locations', $_locations );

			$messages[] = '<div id="message" class="updated notice is-dismissible"><p>' . __( ' locations updated.' ) . '</p></div>';
		}
		break;
}

// Get all nav s.
$nav_menus  = wp_get_nav_menus();
$_count = count( $nav_menus );

// Are we on the add new screen?
$add_new_screen = ( isset( $_GET[''] ) && 0 == $_GET[''] ) ? true : false;

$locations_screen = ( isset( $_GET['action'] ) && 'locations' == $_GET['action'] ) ? true : false;

/*
 * If we have one theme location, and zero s, we take them right
 * into editing their first .
 */
$page_count                  = wp_count_posts( 'page' );
$one_theme_location_no_s = ( 1 == count( get_registered_nav_menus() ) && ! $add_new_screen && empty( $nav_menus ) && ! empty( $page_count->publish ) ) ? true : false;

$nav_menus_l10n = array(
	'oneThemeLocationNos' => $one_theme_location_no_s,
	'moveUp'                  => __( 'Move up one' ),
	'moveDown'                => __( 'Move down one' ),
	'moveToTop'               => __( 'Move to the top' ),
	/* translators: %s: Previous item name. */
	'moveUnder'               => __( 'Move under %s' ),
	/* translators: %s: Previous item name. */
	'moveOutFrom'             => __( 'Move out from under %s' ),
	/* translators: %s: Previous item name. */
	'under'                   => __( 'Under %s' ),
	/* translators: %s: Previous item name. */
	'outFrom'                 => __( 'Out from under %s' ),
	/* translators: 1: Item name, 2: Item position, 3: Total number of items. */
	'Focus'               => __( '%1$s.  item %2$d of %3$d.' ),
	/* translators: 1: Item name, 2: Item position, 3: Parent item name. */
	'subFocus'            => __( '%1$s. Sub item number %2$d under %3$s.' ),
);
wp_localize_script( 'nav-menu', 's', $nav_menus_l10n );

/*
 * Redirect to add screen if there are no s and this users has either zero,
 * or more than 1 theme locations.
 */
if ( 0 == $_count && ! $add_new_screen && ! $one_theme_location_no_s ) {
	wp_redirect( admin_url( 'nav-menus.php?action=edit&=0' ) );
}

// Get recently edited nav .
$recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
if ( empty( $recently_edited ) && is_nav_menu( $nav_menu_selected_id ) ) {
	$recently_edited = $nav_menu_selected_id;
}

// Use $recently_edited if none are selected.
if ( empty( $nav_menu_selected_id ) && ! isset( $_GET[''] ) && is_nav_menu( $recently_edited ) ) {
	$nav_menu_selected_id = $recently_edited;
}

// On deletion of , if another  exists, show it.
if ( ! $add_new_screen && 0 < $_count && isset( $_GET['action'] ) && 'delete' == $_GET['action'] ) {
	$nav_menu_selected_id = $nav_menus[0]->term_id;
}

// Set $nav_menu_selected_id to 0 if no s.
if ( $one_theme_location_no_s ) {
	$nav_menu_selected_id = 0;
} elseif ( empty( $nav_menu_selected_id ) && ! empty( $nav_menus ) && ! $add_new_screen ) {
	// If we have no selection yet, and we have s, set to the first one in the list.
	$nav_menu_selected_id = $nav_menus[0]->term_id;
}

// Update the user's setting.
if ( $nav_menu_selected_id != $recently_edited && is_nav_menu( $nav_menu_selected_id ) ) {
	update_user_meta( $current_user->ID, 'nav_menu_recently_edited', $nav_menu_selected_id );
}

// If there's a , get its name.
if ( ! $nav_menu_selected_title && is_nav_menu( $nav_menu_selected_id ) ) {
	$__object            = wp_get_nav_menu_object( $nav_menu_selected_id );
	$nav_menu_selected_title = ! is_wp_error( $__object ) ? $__object->name : '';
}

// Generate truncated  names.
foreach ( (array) $nav_menus as $key => $_nav_menu ) {
	$nav_menus[ $key ]->truncated_name = wp_html_excerpt( $_nav_menu->name, 40, '&hellip;' );
}

// Retrieve  locations.
if ( current_theme_supports( 's' ) ) {
	$locations      = get_registered_nav_menus();
	$_locations = get_nav_menu_locations();
}

/*
 * Ensure the user will be able to scroll horizontally
 * by adding a class for the max  depth.
 *
 * @global int $_wp_nav_menu_max_depth
 */
global $_wp_nav_menu_max_depth;
$_wp_nav_menu_max_depth = 0;

// Calling wp_get_nav_menu_to_edit generates $_wp_nav_menu_max_depth.
if ( is_nav_menu( $nav_menu_selected_id ) ) {
	$_items  = wp_get_nav_menu_items( $nav_menu_selected_id, array( 'post_status' => 'any' ) );
	$edit_markup = wp_get_nav_menu_to_edit( $nav_menu_selected_id );
}

/**
 * @global int $_wp_nav_menu_max_depth
 *
 * @param string $classes
 * @return string
 */
function wp_nav_menu_max_depth( $classes ) {
	global $_wp_nav_menu_max_depth;
	return "$classes -max-depth-$_wp_nav_menu_max_depth";
}

add_filter( 'admin_body_class', 'wp_nav_menu_max_depth' );

wp_nav_menu_setup();
wp_initial_nav_menu_meta_boxes();

if ( ! current_theme_supports( 's' ) && ! $num_locations ) {
	$messages[] = '<div id="message" class="updated"><p>' . sprintf(
		/* translators: %s: URL to Widgets screen. */
		__( 'Your theme does not natively support s, but you can use them in sidebars by adding a &#8220;Navigation &#8221; widget on the <a href="%s">Widgets</a> screen.' ),
		admin_url( 'widgets.php' )
	) . '</p></div>';
}

if ( ! $locations_screen ) : // Main tab.
	$overview  = '<p>' . __( 'This screen is used for managing your navigation s.' ) . '</p>';
	$overview .= '<p>' . sprintf(
		/* translators: 1: URL to Widgets screen, 2 and 3: The names of the default themes. */
			__( 's can be displayed in locations defined by your theme, even used in sidebars by adding a &#8220;Navigation &#8221; widget on the <a href="%1$s">Widgets</a> screen. If your theme does not support the navigation s feature (the default themes, %2$s and %3$s, do), you can learn about adding this support by following the Documentation link to the side.' ),
		admin_url( 'widgets.php' ),
		'Twenty Nineteen',
		'Twenty Twenty'
	) . '</p>';
	$overview .= '<p>' . __( 'From this screen you can:' ) . '</p>';
	$overview .= '<ul><li>' . __( 'Create, edit, and delete s' ) . '</li>';
	$overview .= '<li>' . __( 'Add, organize, and modify individual  items' ) . '</li></ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' => $overview,
		)
	);

	$_management  = '<p>' . __( 'The  management box at the top of the screen is used to control which  is opened in the editor below.' ) . '</p>';
	$_management .= '<ul><li>' . __( 'To edit an existing , <strong>choose a  from the drop down and click Select</strong>' ) . '</li>';
	$_management .= '<li>' . __( 'If you haven&#8217;t yet created any s, <strong>click the &#8217;create a new &#8217; link</strong> to get started' ) . '</li></ul>';
	$_management .= '<p>' . __( 'You can assign theme locations to individual s by <strong>selecting the desired settings</strong> at the bottom of the  editor. To assign s to all theme locations at once, <strong>visit the Manage Locations tab</strong> at the top of the screen.' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => '-management',
			'title'   => __( ' Management' ),
			'content' => $_management,
		)
	);

	$editing_s  = '<p>' . __( 'Each navigation  may contain a mix of links to pages, categories, custom URLs or other content types.  links are added by selecting items from the expanding boxes in the left-hand column below.' ) . '</p>';
	$editing_s .= '<p>' . __( '<strong>Clicking the arrow to the right of any  item</strong> in the editor will reveal a standard group of settings. Additional settings such as link target, CSS classes, link relationships, and link descriptions can be enabled and disabled via the Screen Options tab.' ) . '</p>';
	$editing_s .= '<ul><li>' . __( 'Add one or several items at once by <strong>selecting the checkbox next to each item and clicking Add to </strong>' ) . '</li>';
	$editing_s .= '<li>' . __( 'To add a custom link, <strong>expand the Custom Links section, enter a URL and link text, and click Add to </strong>' ) . '</li>';
	$editing_s .= '<li>' . __( 'To reorganize  items, <strong>drag and drop items with your mouse or use your keyboard</strong>. Drag or move a  item a little to the right to make it a sub' ) . '</li>';
	$editing_s .= '<li>' . __( 'Delete a  item by <strong>expanding it and clicking the Remove link</strong>' ) . '</li></ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'editing-s',
			'title'   => __( 'Editing s' ),
			'content' => $editing_s,
		)
	);
else : // Locations tab.
	$locations_overview  = '<p>' . __( 'This screen is used for globally assigning s to locations defined by your theme.' ) . '</p>';
	$locations_overview .= '<ul><li>' . __( 'To assign s to one or more theme locations, <strong>select a  from each location&#8217;s drop down.</strong> When you&#8217;re finished, <strong>click Save Changes</strong>' ) . '</li>';
	$locations_overview .= '<li>' . __( 'To edit a  currently assigned to a theme location, <strong>click the adjacent &#8217;Edit&#8217; link</strong>' ) . '</li>';
	$locations_overview .= '<li>' . __( 'To add a new  instead of assigning an existing one, <strong>click the &#8217;Use new &#8217; link</strong>. Your new  will be automatically assigned to that theme location' ) . '</li></ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'locations-overview',
			'title'   => __( 'Overview' ),
			'content' => $locations_overview,
		)
	);
endif;

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/appearance-s-screen/">Documentation on s</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

// Get the admin header.
require_once ABSPATH . 'wp-admin/admin-header.php';
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo esc_html( __( 's' ) ); ?></h1>
	<?php
	if ( current_user_can( 'customize' ) ) :
		$focus = $locations_screen ? array( 'section' => '_locations' ) : array( 'panel' => 'nav_menus' );
		printf(
			' <a class="page-title-action hide-if-no-customize" href="%1$s">%2$s</a>',
			esc_url(
				add_query_arg(
					array(
						array( 'autofocus' => $focus ),
						'return' => urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ),
					),
					admin_url( 'customize.php' )
				)
			),
			__( 'Manage with Live Preview' )
		);
	endif;

	$nav_menutab_active_class = '';
	$nav_menuaria_current     = '';
	if ( ! isset( $_GET['action'] ) || isset( $_GET['action'] ) && 'locations' != $_GET['action'] ) {
		$nav_menutab_active_class = ' nav-menutab-active';
		$nav_menuaria_current     = ' aria-current="page"';
	}
	?>

	<hr class="wp-header-end">

	<nav class="nav-menutab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary ' ); ?>">
		<a href="<?php echo admin_url( 'nav-menus.php' ); ?>" class="nav-menutab<?php echo $nav_menutab_active_class; ?>"<?php echo $nav_menuaria_current; ?>><?php esc_html_e( 'Edit s' ); ?></a>
		<?php
		if ( $num_locations && $_count ) {
			$active_tab_class = '';
			$aria_current     = '';
			if ( $locations_screen ) {
				$active_tab_class = ' nav-menutab-active';
				$aria_current     = ' aria-current="page"';
			}
			?>
			<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'locations' ), admin_url( 'nav-menus.php' ) ) ); ?>" class="nav-menutab<?php echo $active_tab_class; ?>"<?php echo $aria_current; ?>><?php esc_html_e( 'Manage Locations' ); ?></a>
			<?php
		}
		?>
	</nav>
	<?php
	foreach ( $messages as $message ) :
		echo $message . "\n";
	endforeach;
	?>
	<?php
	if ( $locations_screen ) :
		if ( 1 == $num_locations ) {
			echo '<p>' . __( 'Your theme supports one . Select which  you would like to use.' ) . '</p>';
		} else {
			echo '<p>' . sprintf(
				/* translators: %s: Number of s. */
				_n(
					'Your theme supports %s . Select which  appears in each location.',
					'Your theme supports %s s. Select which  appears in each location.',
					$num_locations
				),
				number_format_i18n( $num_locations )
			) . '</p>';
		}
		?>
	<div id="-locations-wrap">
		<form method="post" action="<?php echo esc_url( add_query_arg( array( 'action' => 'locations' ), admin_url( 'nav-menus.php' ) ) ); ?>">
			<table class="widefat fixed" id="-locations-table">
				<thead>
				<tr>
					<th scope="col" class="manage-column column-locations"><?php _e( 'Theme Location' ); ?></th>
					<th scope="col" class="manage-column column-s"><?php _e( 'Assigned ' ); ?></th>
				</tr>
				</thead>
				<tbody class="-locations">
				<?php foreach ( $locations as $_location => $_name ) { ?>
					<tr class="-locations-row">
						<td class="-location-title"><label for="locations-<?php echo $_location; ?>"><?php echo $_name; ?></label></td>
						<td class="-location-s">
							<select name="-locations[<?php echo $_location; ?>]" id="locations-<?php echo $_location; ?>">
								<option value="0"><?php printf( '&mdash; %s &mdash;', esc_html__( 'Select a ' ) ); ?></option>
								<?php
								foreach ( $nav_menus as $a) :
									$data_orig = '';
									$selected  = isset( $_locations[ $_location ] ) && $_locations[ $_location ] == $a->term_id;
									if ( $selected ) {
										$data_orig = 'data-orig="true"';
									}
									?>
									<option <?php echo $data_orig; ?> <?php selected( $selected ); ?> value="<?php echo $a->term_id; ?>">
										<?php echo wp_html_excerpt( $a->name, 40, '&hellip;' ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="locations-row-links">
								<?php if ( isset( $_locations[ $_location ] ) && 0 != $_locations[ $_location ] ) : ?>
								<span class="locations-edit--link">
									<a href="
									<?php
									echo esc_url(
										add_query_arg(
											array(
												'action' => 'edit',
												''   => $_locations[ $_location ],
											),
											admin_url( 'nav-menus.php' )
										)
									);
									?>
									">
										<span aria-hidden="true"><?php _ex( 'Edit', '' ); ?></span><span class="screen-reader-text"><?php _e( 'Edit selected ' ); ?></span>
									</a>
								</span>
								<?php endif; ?>
								<span class="locations-add--link">
									<a href="
									<?php
									echo esc_url(
										add_query_arg(
											array(
												'action' => 'edit',
												''   => 0,
												'use-location' => $_location,
											),
											admin_url( 'nav-menus.php' )
										)
									);
									?>
									">
										<?php _ex( 'Use new ', '' ); ?>
									</a>
								</span>
							</div><!-- .locations-row-links -->
						</td><!-- .-location-s -->
					</tr><!-- .-locations-row -->
				<?php } // End foreach. ?>
				</tbody>
			</table>
			<p class="button-controls wp-clearfix"><?php submit_button( __( 'Save Changes' ), 'primary left', 'nav-menu-locations', false ); ?></p>
			<?php wp_nonce_field( 'save--locations' ); ?>
			<input type="hidden" name="" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
		</form>
	</div><!-- #-locations-wrap -->
		<?php
		/**
		 * Fires after the  locations table is displayed.
		 *
		 * @since 3.6.0
		 */
		do_action( 'after__locations_table' );
		?>
	<?php else : ?>
	<div class="manage-s">
		<?php if ( $_count < 1 ) : ?>
		<span class="first--message">
			<?php _e( 'Create your first  below.' ); ?>
			<span class="screen-reader-text"><?php _e( 'Fill in the  Name and click the Create  button to create your first .' ); ?></span>
		</span><!-- /first--message -->
		<?php elseif ( $_count < 2 ) : ?>
		<span class="add-edit--action">
			<?php
			printf(
				/* translators: %s: URL to create a new . */
				__( 'Edit your  below, or <a href="%s">create a new </a>. Don&#8217;t forget to save your changes!' ),
				esc_url(
					add_query_arg(
						array(
							'action' => 'edit',
							''   => 0,
						),
						admin_url( 'nav-menus.php' )
					)
				)
			);
			?>
			<span class="screen-reader-text"><?php _e( 'Click the Save  button to save your changes.' ); ?></span>
		</span><!-- /add-edit--action -->
		<?php else : ?>
			<form method="get" action="<?php echo admin_url( 'nav-menus.php' ); ?>">
			<input type="hidden" name="action" value="edit" />
			<label for="select--to-edit" class="selected-"><?php _e( 'Select a  to edit:' ); ?></label>
			<select name="" id="select--to-edit">
				<?php if ( $add_new_screen ) : ?>
					<option value="0" selected="selected"><?php _e( '&mdash; Select &mdash;' ); ?></option>
				<?php endif; ?>
				<?php foreach ( (array) $nav_menus as $_nav_menu ) : ?>
					<option value="<?php echo esc_attr( $_nav_menu->term_id ); ?>" <?php selected( $_nav_menu->term_id, $nav_menu_selected_id ); ?>>
						<?php
						echo esc_html( $_nav_menu->truncated_name );

						if ( ! empty( $_locations ) && in_array( $_nav_menu->term_id, $_locations ) ) {
							$locations_assigned_to_this_ = array();
							foreach ( array_keys( $_locations, $_nav_menu->term_id ) as $_location_key ) {
								if ( isset( $locations[ $_location_key ] ) ) {
									$locations_assigned_to_this_[] = $locations[ $_location_key ];
								}
							}

							/**
							 * Filters the number of locations listed per  in the drop-down select.
							 *
							 * @since 3.6.0
							 *
							 * @param int $locations Number of  locations to list. Default 3.
							 */
							$assigned_locations = array_slice( $locations_assigned_to_this_, 0, absint( apply_filters( 'wp_nav_menulocations_listed_per_', 3 ) ) );

							// Adds ellipses following the number of locations defined in $assigned_locations.
							if ( ! empty( $assigned_locations ) ) {
								printf(
									' (%1$s%2$s)',
									implode( ', ', $assigned_locations ),
									count( $locations_assigned_to_this_ ) > count( $assigned_locations ) ? ' &hellip;' : ''
								);
							}
						}
						?>
					</option>
				<?php endforeach; ?>
			</select>
			<span class="submit-btn"><input type="submit" class="button" value="<?php esc_attr_e( 'Select' ); ?>"></span>
			<span class="add-new--action">
				<?php
				printf(
					/* translators: %s: URL to create a new . */
					__( 'or <a href="%s">create a new </a>. Don&#8217;t forget to save your changes!' ),
					esc_url(
						add_query_arg(
							array(
								'action' => 'edit',
								''   => 0,
							),
							admin_url( 'nav-menus.php' )
						)
					)
				);
				?>
				<span class="screen-reader-text"><?php _e( 'Click the Save  button to save your changes.' ); ?></span>
			</span><!-- /add-new--action -->
		</form>
			<?php
		endif;

		$metabox_holder_disabled_class = '';
		if ( isset( $_GET[''] ) && '0' == $_GET[''] ) {
			$metabox_holder_disabled_class = ' metabox-holder-disabled';
		}
		?>
	</div><!-- /manage-s -->
	<div id="nav-menus-frame" class="wp-clearfix">
	<div id="-settings-column" class="metabox-holder<?php echo $metabox_holder_disabled_class; ?>">

		<div class="clear"></div>

		<form id="nav-menu-meta" class="nav-menu-meta" method="post" enctype="multipart/form-data">
			<input type="hidden" name="" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
			<input type="hidden" name="action" value="add--item" />
			<?php wp_nonce_field( 'add-_item', '-settings-column-nonce' ); ?>
			<h2><?php _e( 'Add  items' ); ?></h2>
			<?php do_accordion_sections( 'nav-menus', 'side', null ); ?>
		</form>

	</div><!-- /#-settings-column -->
	<div id="-management-liquid">
		<div id="-management">
			<form id="update-nav-menu" method="post" enctype="multipart/form-data">
			<?php
				$new_screen_class = '';
			if ( $add_new_screen ) {
				$new_screen_class = 'blank-slate';
			}
			?>
				<h2><?php _e( ' structure' ); ?></h2>
				<div class="-edit <?php echo $new_screen_class; ?>">
					<input type="hidden" name="nav-menu-data">
					<?php
					wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
					wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
					wp_nonce_field( 'update-nav_menu', 'update-nav-menu-nonce' );

					$_name_aria_desc = $add_new_screen ? ' aria-describedby="-name-desc"' : '';

					if ( $one_theme_location_no_s ) {
						$_name_val = 'value="' . esc_attr( ' 1' ) . '"';
						?>
						<input type="hidden" name="zero--state" value="true" />
						<?php
					} else {
						$_name_val = 'value="' . esc_attr( $nav_menu_selected_title ) . '"';
					}
					?>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="" id="" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
					<div id="nav-menu-header">
						<div class="major-publishing-actions wp-clearfix">
							<label class="-name-label" for="-name"><?php _e( ' Name' ); ?></label>
							<input name="-name" id="-name" type="text" class="-name regular-text -item-textbox" <?php echo $_name_val . $_name_aria_desc; ?> />
							<div class="publishing-action">
								<?php submit_button( empty( $nav_menu_selected_id ) ? __( 'Create ' ) : __( 'Save ' ), 'primary large -save', 'save_', false, array( 'id' => 'save__header' ) ); ?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- END .nav-menu-header -->
					<div id="post-body">
						<div id="post-body-content" class="wp-clearfix">
							<?php if ( ! $add_new_screen ) : ?>
								<?php
								$hide_style = '';
								if ( isset( $_items ) && 0 == count( $_items ) ) {
									$hide_style = 'style="display: none;"';
								}

								if ( $one_theme_location_no_s ) {
									$starter_copy = __( 'Edit your default  by adding or removing items. Drag the items into the order you prefer. Click Create  to save your changes.' );
								} else {
									$starter_copy = __( 'Drag the items into the order you prefer. Click the arrow on the right of the item to reveal additional configuration options.' );
								}
								?>
							<div class="drag-instructions post-body-plain" <?php echo $hide_style; ?>>
								<p><?php echo $starter_copy; ?></p>
							</div>
								<?php
								if ( isset( $edit_markup ) && ! is_wp_error( $edit_markup ) ) {
									echo $edit_markup;
								} else {
									?>
							<ul class="" id="-to-edit"></ul>
								<?php } ?>
							<?php endif; ?>
							<?php if ( $add_new_screen ) : ?>
								<p class="post-body-plain" id="-name-desc"><?php _e( 'Give your  a name, then click Create .' ); ?></p>
								<?php if ( isset( $_GET['use-location'] ) ) : ?>
									<input type="hidden" name="use-location" value="<?php echo esc_attr( $_GET['use-location'] ); ?>" />
								<?php endif; ?>
								<?php
								endif;

								$no_s_style = '';
							if ( $one_theme_location_no_s ) {
								$no_s_style = 'style="display: none;"';
							}
							?>
							<div class="-settings" <?php echo $no_s_style; ?>>
								<h3><?php _e( ' Settings' ); ?></h3>
								<?php
								if ( ! isset( $auto_add ) ) {
									$auto_add = get_option( 'nav_menu_options' );
									if ( ! isset( $auto_add['auto_add'] ) ) {
										$auto_add = false;
									} elseif ( false !== array_search( $nav_menu_selected_id, $auto_add['auto_add'] ) ) {
										$auto_add = true;
									} else {
										$auto_add = false;
									}
								}
								?>

								<fieldset class="-settings-group auto-add-pages">
									<legend class="-settings-group-name howto"><?php _e( 'Auto add pages' ); ?></legend>
									<div class="-settings-input checkbox-input">
										<input type="checkbox"<?php checked( $auto_add ); ?> name="auto-add-pages" id="auto-add-pages" value="1" /> <label for="auto-add-pages"><?php printf( __( 'Automatically add new top-level pages to this ' ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ); ?></label>
									</div>
								</fieldset>

								<?php if ( current_theme_supports( 's' ) ) : ?>

									<fieldset class="-settings-group -theme-locations">
										<legend class="-settings-group-name howto"><?php _e( 'Display location' ); ?></legend>
										<?php foreach ( $locations as $location => $description ) : ?>
										<div class="-settings-input checkbox-input">
											<input type="checkbox"<?php checked( isset( $_locations[ $location ] ) && $_locations[ $location ] == $nav_menu_selected_id ); ?> name="-locations[<?php echo esc_attr( $location ); ?>]" id="locations-<?php echo esc_attr( $location ); ?>" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
											<label for="locations-<?php echo esc_attr( $location ); ?>"><?php echo $description; ?></label>
											<?php if ( ! empty( $_locations[ $location ] ) && $_locations[ $location ] != $nav_menu_selected_id ) : ?>
												<span class="theme-location-set">
												<?php
													printf(
														/* translators: %s:  name. */
														_x( '(Currently set to: %s)', ' location' ),
														wp_get_nav_menu_object( $_locations[ $location ] )->name
													);
												?>
												</span>
											<?php endif; ?>
										</div>
										<?php endforeach; ?>
									</fieldset>

								<?php endif; ?>

							</div>
						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->
					<div id="nav-menu-footer">
						<div class="major-publishing-actions wp-clearfix">
							<?php if ( 0 != $_count && ! $add_new_screen ) : ?>
							<span class="delete-action">
								<a class="submitdelete deletion -delete" href="
								<?php
								echo esc_url(
									wp_nonce_url(
										add_query_arg(
											array(
												'action' => 'delete',
												''   => $nav_menu_selected_id,
											),
											admin_url( 'nav-menus.php' )
										),
										'delete-nav_menu-' . $nav_menu_selected_id
									)
								);
								?>
								"><?php _e( 'Delete ' ); ?></a>
							</span><!-- END .delete-action -->
							<?php endif; ?>
							<div class="publishing-action">
								<?php submit_button( empty( $nav_menu_selected_id ) ? __( 'Create ' ) : __( 'Save ' ), 'primary large -save', 'save_', false, array( 'id' => 'save__footer' ) ); ?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- /#nav-menu-footer -->
				</div><!-- /.-edit -->
			</form><!-- /#update-nav-menu -->
		</div><!-- /#-management -->
	</div><!-- /#-management-liquid -->
	</div><!-- /#nav-menus-frame -->
	<?php endif; ?>
</div><!-- /.wrap-->
<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
