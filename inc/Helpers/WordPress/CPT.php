<?php
namespace RecipeRemix\Helpers\WordPress;

class CPT {

    public function __construct() {

        add_action( 'init', __CLASS__ . '::register_cpt' , 10 );
    }

    public static function register_cpt() {

        self::register();
    }



    private static function register( ) {

        /**
         * Post Type: Recipes.
         */
    
        $labels = [
            "name" => esc_html__( "Recipes", "custom-post-type-ui" ),
            "singular_name" => esc_html__( "Recipe", "custom-post-type-ui" ),
        ];
    
        $args = [
            "label" => esc_html__( "Recipes", "custom-post-type-ui" ),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "rest_namespace" => "wp/v2",
            "has_archive" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "can_export" => false,
            "rewrite" => [ "slug" => "recipe", "with_front" => true ],
            "query_var" => true,
            "menu_icon" => "dashicons-drumstick",
            "supports" => [ "title", "editor", "thumbnail", "revisions", "author" ],
            "show_in_graphql" => false,
        ];
    
        register_post_type( "recipe", $args );
    }
    
    
}