<?php

namespace RecipeScraperTests\Scrapers;

use RecipeScraperTests\ScraperTestCase;
use RecipeScraper\Scrapers\HearstDigitalMedia;

class WwwWomansDayComTest extends ScraperTestCase
{
    protected function getHost()
    {
        return 'www.womansday.com';
    }

    protected function makeScraper()
    {
        return new HearstDigitalMedia;
    }
}
