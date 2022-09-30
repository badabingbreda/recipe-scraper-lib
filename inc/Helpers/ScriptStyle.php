<?php
namespace RecipeRemix\Helpers;

class ScriptStyle {

    public function __construct() {
        add_action( 'wp_enqueue_scripts' , __CLASS__ . '::register_scripts' );
    }

    public static function register_scripts() {

        wp_enqueue_script('reciperemix', RECIPEREMIX_URL . '/js/reciperemix.js', null, RECIPEREMIX_VERSION, true );
        wp_enqueue_style('reciperemix',RECIPEREMIX_URL . '/css/reciperemix.min.css' ,null,RECIPEREMIX_VERSION,'all' );
    }
}