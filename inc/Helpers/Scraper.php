<?php
namespace RecipeRemix\Helpers;

use RecipeRemix\Status;

class Scraper {
    
    /**
     * testExists
     *
     * Test if a recipe already exists that used this as the base recipe
     * 
     * @param  mixed $original_recipe_url
     * @return void
     */
    public static function testExists( $original_recipe_url ) {
        
        $args = array(
            'post_type' => 'recipe',
            'numberposts' => 1,
            'meta_query' => array(
                'relation' => 'AND',
                'originalurl_clause' => array(
                    'key' => 'originalurl',
                    'value' => $original_recipe_url,
                    'compare' => '='
                )
            ),
            'fields' => 'ids',
        );

        $posts = \get_posts( $args );

        if ( sizeof( $posts ) > 0 ) return $posts[0];

        return false;
    }

    public static function insertRecipe( $recipe , $original_recipe_url ) {

        // create a post, returning the postid;
        $args = array(
            'post_type' => 'recipe',
            'post_title' => $recipe[ 'name' ],
            'post_status' => 'publish',
        );

        $postid = wp_insert_post( $args );

        if ( is_a( $postid , 'WP_Error' ) ) {

            return new Status( 500 );
            
        } else {

            \update_field( 'preptime' , $recipe[ 'prepTime' ] , $postid );
            \update_field( 'cooktime' , $recipe[ 'cookTime' ] , $postid );
            \update_field( 'totaltime' , $recipe[ 'totalTime' ] , $postid );
            \update_field( 'description' , $recipe[ 'description' ] , $postid );
            \update_field( 'image' , $recipe[ 'image' ] , $postid );
            \update_field( 'yield' , $recipe[ 'yield' ] , $postid );
            \update_field( 'ingredients' , array_map( function( $item ) { return array('ingredient' => $item );  } , $recipe[ 'ingredients' ] ) , $postid );
            \update_field( 'instructions' , array_map( function( $item ) { return array('instruction' => $item );  } , $recipe[ 'instructions' ] ) , $postid );
            \update_field( 'originalurl' , $original_recipe_url , $postid );
            

            return $postid;
        }

        // $recipe['author'] // string|null
        // $recipe['categories'] // string[]|null
        // $recipe['cookingMethod'] // string|null
        // $recipe['cookTime'] // string|null
        // $recipe['cuisines'] // string[]|null
        // $recipe['description'] // string|null
        // $recipe['image'] // string|null
        // $recipe['ingredients'] // string[]|null
        // $recipe['instructions'] // string[]|null
        // $recipe['name'] // string|null
        // $recipe['notes'] // string[]|null
        // $recipe['prepTime'] // string|null
        // $recipe['publisher'] // string|null
        // $recipe['totalTime'] // string|null
        // $recipe['url'] // string|null
        // $recipe['yield'] // string|null        

        // return postid;
    }


}