<?php
namespace RecipeRemix\Helpers;

use RecipeRemix\Helpers\WordPress\CPT;
use RecipeRemix\Helpers\WordPress\Taxonomy;

class WordPress {

    public function __construct() {

        new CPT();
        new Taxonomy();

    }
}