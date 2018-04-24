<?php

function illdy_child_enqueue_styles() {
    $parent_style = 'illdy'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $parent_theme_dir = get_template_directory_uri();
    $child_theme_dir = get_stylesheet_directory_uri();

    wp_enqueue_style( $parent_style, $parent_theme_dir . '/style.css');
    wp_enqueue_style( 'illdy-child', $child_theme_dir . '/style.css', array( $parent_style ), wp_get_theme()->get('Version'));
    if (is_page('translations')) {
      wp_enqueue_style( 'jquery-accordion', $child_theme_dir . '/layout/css/jquery-ui.min.css', array( $parent_style ));
    }
}
add_action( 'wp_enqueue_scripts', 'illdy_child_enqueue_styles' );

function illdy_child_enqueue_scripts()
{
    $parent_theme_dir = get_template_directory_uri();
    $child_theme_dir = get_stylesheet_directory_uri();

    if (is_page('homepage')) {
      wp_register_script( 'homepage', $child_theme_dir . '/layout/js/homepage.js', array( 'jquery' ), NULL, true);
      wp_enqueue_script( 'homepage' );
    }

    if (is_page('translations')) {
        wp_register_script( 'stickyfill', $child_theme_dir . '/layout/js/stickyfill.js', array( 'jquery' ), NULL, true);
        wp_register_script( 'translations-page', $child_theme_dir . '/layout/js/translations_page.js', array( 'jquery', 'stickyfill', 'jquery-ui-accordion' ), NULL, true);
        wp_localize_script( 'translations-page', 'WPURL', array( 'siteurl' => $child_theme_dir ));
        wp_enqueue_script( ' stickyfill' );
        wp_enqueue_script( 'translations-page' );
        // wp_enqueue_script( 'wp-url' );
    }

    // Use same handle as parent theme to override ('illdy-plugins')
    // (leongv) This fixes the problem with counter section on the front page where
    // jQuery's visible() doesn't fire correctly on Chrome, which results in the
    // counter numbers not showing even though it's visible on the screen.
    wp_enqueue_script( 'illdy-plugins', $child_theme_dir . '/layout/js/plugins.js', array( 'jquery' ), '1.0.16', true);
}
add_action( 'wp_enqueue_scripts', 'illdy_child_enqueue_scripts' );

?>
<?php

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION
