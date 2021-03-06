<?php

/**
 * Handles logic for the post edit screen.
 *
 * @since 1.0
 */
final class FLBuilderAdminPosts {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'current_screen',                __CLASS__ . '::init_rendering' );

		/* Filters */
		add_filter( 'redirect_post_location',        __CLASS__ . '::redirect_post_location' );
		add_filter( 'page_row_actions',              __CLASS__ . '::render_row_actions_link' );
		add_filter( 'post_row_actions',              __CLASS__ . '::render_row_actions_link' );
	}

	/**
	 * WordPress doesn't have a "right way" to get the current
	 * post type being edited and the new editor doesn't make
	 * this any easier. This method attempts to fix that.
	 *
	 * @since 2.1
	 * @return void
	 */
	static public function get_post_type() {
		global $post, $typenow, $current_screen;

		if ( is_object( $post ) && $post->post_type ) {
			return $post->post_type;
		} elseif ( $typenow ) {
			return $typenow;
		} elseif ( is_object( $current_screen ) && $current_screen->post_type ) {
			return $current_screen->post_type;
		} elseif ( isset( $_REQUEST['post_type'] ) ) {
			return sanitize_key( $_REQUEST['post_type'] );
		}

		return null;
	}

	/**
	 * Sets the body class, loads assets and renders the UI
	 * if we are on a post type that supports the builder.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init_rendering() {
		global $pagenow;

		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

			$render_ui  = apply_filters( 'fl_builder_render_admin_edit_ui', true );
			$post_types = FLBuilderModel::get_post_types();

			if ( $render_ui && in_array( self::get_post_type(), $post_types ) ) {
				add_filter( 'admin_body_class',         __CLASS__ . '::body_class', 99 );
				add_action( 'admin_enqueue_scripts',    __CLASS__ . '::styles_scripts' );
				add_action( 'edit_form_after_title',    __CLASS__ . '::render' );
			}
		}
	}

	/**
	 * Enqueues the CSS/JS for the post edit screen.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function styles_scripts() {
		global $wp_version;

		// Styles
		wp_enqueue_style( 'fl-builder-admin-posts', FL_BUILDER_URL . 'css/fl-builder-admin-posts.css', array(), FL_BUILDER_VERSION );

		// Legacy WP Styles (3.7 and below)
		if ( version_compare( $wp_version, '3.7', '<=' ) ) {
			wp_enqueue_style( 'fl-builder-admin-posts-legacy', FL_BUILDER_URL . 'css/fl-builder-admin-posts-legacy.css', array(), FL_BUILDER_VERSION );
		}

		// Scripts
		wp_enqueue_script( 'json2' );
		wp_enqueue_script( 'fl-builder-admin-posts', FL_BUILDER_URL . 'js/fl-builder-admin-posts.js', array(), FL_BUILDER_VERSION );
	}

	/**
	 * Adds classes to the post edit screen body class.
	 *
	 * @since 1.0
	 * @param string $classes The existing body classes.
	 * @return string The body classes.
	 */
	static public function body_class( $classes = '' ) {
		global $wp_version;

		// Builder body class
		if ( FLBuilderModel::is_builder_enabled() ) {
			$classes .= ' fl-builder-enabled';
		}

		// Pre WP 3.8 body class
		if ( version_compare( $wp_version, '3.8', '<' ) ) {
			$classes .= ' fl-pre-wp-3-8';
		}

		return $classes;
	}

	/**
	 * Renders the HTML for the post edit screen.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render() {
		global $post;

		$post_type_obj 	= get_post_type_object( $post->post_type );
		$post_type_name = strtolower( $post_type_obj->labels->singular_name );
		$enabled 		= FLBuilderModel::is_builder_enabled();

		include FL_BUILDER_DIR . 'includes/admin-posts.php';
	}

	/**
	 * Renders the action link for post listing pages.
	 *
	 * @since 1.0
	 * @param array $actions
	 * @return array The array of action data.
	 */
	static public function render_row_actions_link( $actions = array() ) {
		global $post;

		if ( 'trash' != $post->post_status && current_user_can( 'edit_post', $post->ID ) && wp_check_post_lock( $post->ID ) === false ) {

			$is_post_editable = (bool) apply_filters( 'fl_builder_is_post_editable', true, $post );
			$user_access = FLBuilderUserAccess::current_user_can( 'builder_access' );
			$post_types = FLBuilderModel::get_post_types();

			if ( in_array( $post->post_type, $post_types ) && $is_post_editable && $user_access ) {
				$enabled = get_post_meta( $post->ID, '_fl_builder_enabled', true );
				$dot = ' <span style="color:' . ( $enabled ? '#6bc373' : '#d9d9d9' ) . '; font-size:18px;">&bull;</span>';
				$actions['fl-builder'] = '<a href="' . FLBuilderModel::get_edit_url() . '">' . FLBuilderModel::get_branding() . $dot . '</a>';
			}
		}

		return $actions;
	}

	/**
	 * Where to redirect this post on save.
	 *
	 * @since 1.0
	 * @param string $location
	 * @return string The location to redirect this post on save.
	 */
	static public function redirect_post_location( $location ) {
		if ( isset( $_POST['fl-builder-redirect'] ) ) {
			$location = FLBuilderModel::get_edit_url( absint( $_POST['fl-builder-redirect'] ) );
		}

		return $location;
	}
}

FLBuilderAdminPosts::init();
