<?php
namespace RecipeRemix\Helpers;

class ScriptStyle {

    public function __construct() {
        add_action( 'wp_enqueue_scripts' , __CLASS__ . '::register_scripts' );
    }

    public static function register_scripts() {

        wp_enqueue_script('onpageeditor', RECIPEREMIX_URL . '/js/onpageeditor.js', null, RECIPEREMIX_VERSION, true );
        wp_enqueue_script('reciperemix', RECIPEREMIX_URL . '/js/reciperemix.js', null, RECIPEREMIX_VERSION, true );
        wp_enqueue_script('sortable', RECIPEREMIX_URL . '/js/sortable.min.js', null, RECIPEREMIX_VERSION, false );
        wp_enqueue_style('onpageeditor',RECIPEREMIX_URL . '/css/onpageeditor.min.css' ,null,RECIPEREMIX_VERSION,'all' );
    }
}