<?php

namespace RecipeScraper\Scrapers;

use RecipeScraper\Arr;
use Symfony\Component\DomCrawler\Crawler;
use RecipeScraper\ExtractsDataFromCrawler;

/**
 * @todo Need to find recipes with times to test against.
 */
class WwwMyRecipesCom extends SchemaOrgJsonLd
{
    use ExtractsDataFromCrawler;

    public function supports(Crawler $crawler) : bool
    {
        return parent::supports($crawler)
            && 'www.myrecipes.com' === parse_url($crawler->getUri(), PHP_URL_HOST);
    }

    protected function extractIngredients(Crawler $crawler, array $json)
    {
        // There are some weird issues around UoM in LD+JSON - revert to markup.
        return $this->extractArray($crawler, '[itemprop="recipeIngredient"]');
    }

    protected function extractUrl(Crawler $crawler, array $json)
    {
        if (is_string($url = Arr::get($json, 'mainEntityOfPage.@id'))) {
            return $url;
        }

        return null;
    }

    protected function postNormalizeAuthor($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        return strip_tags($value);
    }

    protected function postNormalizeInstructions($value)
    {
        if (! is_array($value) || ! Arr::ofStrings($value)) {
            return $value;
        }

        return array_map(function ($val) {
            return trim(strip_tags($val));
        }, $value);
    }
}
