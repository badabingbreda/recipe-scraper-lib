<?php
namespace RecipeRemix\Integration;

class ACFExtended {

    public function __construct() {

        add_filter( 'acfe/form/load' , __CLASS__ . '::try_custom_html' , 10 , 2 );
        
    }

    public static function try_custom_html( $args , $post_id ) {

        if ( !class_exists( 'Timber' ) ) return $args;

        $content = '';
        
        $context = \Timber::get_context();
        $context[ 'post_id' ] = $post_id;
        $context[ 'form_args' ] = $args;

        if ( 
            $args[ 'custom_html_enabled' ] && 
            $args[ 'form_attributes' ][ 'id' ] == 'recipe-remix-form' 
        ) {

            $content = \RecipeRemix\Integration\Timber::render_ignore( $args[ 'name' ] . '.twig' , $context );
            if ( $content ) $args[ 'custom_html' ] = $content; 
            

            
        }

        $context = \Timber::get_context();
        $context[ 'post_id' ] = $post_id;
        $context[ 'form_args' ] = $args;
        $content = \RecipeRemix\Integration\Timber::render_ignore( $args[ 'name' ] . '-success.twig' , $context );
        if ( $content ) $args[ 'html_updated_message' ] = $content;

        return $args;

    }
}