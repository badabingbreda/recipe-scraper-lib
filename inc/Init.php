<?php
namespace RecipeRemix;

use RecipeRemix\Helpers\WordPress;
use RecipeRemix\Helpers\ScriptStyle;
use RecipeRemix\Helpers\Ajax;

use RecipeRemix\Integration\ACF;
use RecipeRemix\Integration\ACFExtended;
use RecipeRemix\Integration\Timber;


class Init {

    public function __construct() {

        new WordPress();
        new ScriptStyle();
        new Ajax();

        new ACF();
        new ACFExtended();
        new Timber();

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