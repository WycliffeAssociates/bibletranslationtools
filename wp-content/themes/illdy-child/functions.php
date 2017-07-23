<?php

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parent_style = 'illdy'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style(
      $parent_style,
      get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
      'illdy-child',
      get_stylesheet_directory_uri() . '/style.css',
      array( $parent_style ),
      wp_get_theme()->get('Version')
    );
}

// (leongv) This fixes the problem with counter section on the front page where
// jQuery's visible() doesn't fire correctly on Chrome, which results in the
// counter numbers not showing even though it's visible on the screen.
add_action( 'wp_enqueue_scripts', 'illdy_plugin_fix' );
function illdy_plugin_fix()
{
    // Use same handle as parent theme to override ('illdy-plugins')
    wp_enqueue_script(
      'illdy-plugins',
      get_stylesheet_directory_uri() . '/layout/js/plugins.min.js',
      array( 'jquery' ),
      '1.0.16',
      true
    );
}

?>
<?php

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION
