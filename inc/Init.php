<?php
namespace RecipeRemix;

use RecipeRemix\Helpers\ScriptStyle;
use RecipeRemix\Helpers\Ajax;

class Init {

    public function __construct() {

        new ScriptStyle();
        new Ajax();

        //add_action( 'init' , __CLASS__ . '::guzzle' );

    }

    public static function guzzle() {

        $client = new \Goutte\Client;
        $crawler = $client->request('GET', 'http://allrecipes.com/recipe/139917/joses-shrimp-ceviche/');

        $scraper = \RecipeScraper\Factory::make();
        

        $recipe = $scraper->scrape($crawler);

        var_dump( $recipe );

    }
}