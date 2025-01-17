<?php

namespace RecipeScraper\Scrapers;

use RecipeScraper\Arr;
use Symfony\Component\DomCrawler\Crawler;

/**
 * We lose out on ingredient group titles by using LD+JSON.
 *
 * Has notes but they are stuffed in with instructions.
 *
 * Template *seems* to be set up to allow for multiple contributors/authors per recipe - should keep
 * an eye out for any recipes where this is the case.
 */
class WwwAmbitiousKitchenCom extends SchemaOrgJsonLd
{
    /**
     * @param  Crawler $crawler
     * @return boolean
     */
    public function supports(Crawler $crawler) : bool
    {
        return parent::supports($crawler)
            && 'www.ambitiouskitchen.com' === parse_url($crawler->getUri(), PHP_URL_HOST);
    }

    /**
     * @param  Crawler $crawler
     * @param  array   $json
     * @return string|null
     */
    protected function extractAuthor(Crawler $crawler, array $json)
    {
        // Seems to include author, company, city and state. Also used for photo credit...
        return $this->extractString($crawler, '.contributor-name');
    }

    /**
     * @param  Crawler $crawler
     * @param  array   $json
     * @return string[]|null
     */
    protected function extractCategories(Crawler $crawler, array $json)
    {
        // May not all be appropriate... Seems to include ingredients, course, etc.
        return $this->extractArray($crawler, '.keyword');
    }

    /**
     * @param  Crawler $crawler
     * @param  array   $json
     * @return string[]|null
     */
    protected function extractInstructions(Crawler $crawler, array $json)
    {
        $instructions = Arr::get($json, 'recipeInstructions');

        if (! is_array($instructions)) {
            return $this->extractArray($crawler, '[itemprop="recipeInstructions"] [itemprop="itemListElement"] [itemprop="text"]');
        }

        $instructions = array_filter($instructions, function ($instruction) {
            return Arr::ofStrings($instruction) && array_key_exists('text', $instruction);
        });

        return array_column($instructions, 'text');
    }

    /**
     * @param  Crawler $crawler
     * @param  array   $json
     * @return string|null
     */
    protected function extractUrl(Crawler $crawler, array $json)
    {
        return $this->extractString($crawler, '[rel="canonical"]', ['href']);
    }
}
