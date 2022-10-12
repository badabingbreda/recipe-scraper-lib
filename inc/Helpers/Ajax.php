<?php
namespace RecipeRemix\Helpers;

use Goutte\Client;
use RecipeRemix\Status;
use RecipeRemix\Helpers\Scraper;

class Ajax {

    public function __construct() {

        add_action( 'wp_ajax_scrape_recipe' , __CLASS__ . '::scrape_recipe' );
        add_action( 'wp_ajax_update_recipe' , __CLASS__ . '::update_recipe' );
    }

    public static function scrape_recipe() {

        if ( !defined( 'DOING_AJAX' ) )  DEFINE( 'DOING_AJAX' , true );

        // add test if is member at some time

        $client = new \Goutte\Client;
        $crawler = $client->request('GET', $_REQUEST[ 'url' ] );
        $recipe = null;
        $scraper = \RecipeScraper\Factory::make();

        // crawler is supported
        if ( $scraper->supports($crawler) ) {

            if ( !Scraper::testExists( $_REQUEST[ 'url' ]) ) {

                $recipe = $scraper->scrape($crawler);
                
                $postid = Scraper::insertRecipe( $recipe , $_REQUEST[ 'url' ] );

                if ( !is_a( $postid , 'Status' ) ) {

                    // create new status and message
                    $status = new Status( 200 , [ 'message' => 'success' ] );

                } else {

                    $status = $postid;
                }
                
            } else {
                
                // create new status and message
                $status = new Status( 204 , [ 'message' => 'success but exists' ] );

            }
            

            $return = array(
                "recipe" => $recipe,
                "recipeLink" => \get_the_permalink( $postid ),
                "status" => $status->getStatus(),
            );

        } else {

            // create new status and message
            $status = new Status( 400 , [ 'message' => 'failed' ] );

            $return = array(
                "status" => $status->getStatus(),
            );

        }

        echo wp_json_encode( $return );

        wp_die();

    }

    public static function update_recipe() {

        if ( !defined( 'DOING_AJAX' ) )  DEFINE( 'DOING_AJAX' , true );

        $status = new Status( 200 , [ 'message' => 'success' ] );

        $reciperemix = $_REQUEST[ 'collection' ];

        foreach( $reciperemix as $name => $value) {
            \update_field( $name , $value , $_REQUEST[ 'postid' ] );
        }

        $return = array(
            "postid" => $_REQUEST[ 'postid' ],
            "recipe" => $_REQUEST[ 'collection' ],
            "status" => $status->getStatus(),
        );

        echo wp_json_encode( $return );

        wp_die();

    }
}