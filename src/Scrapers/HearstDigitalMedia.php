<?php

namespace RecipeScraper\Scrapers;

use Symfony\Component\DomCrawler\Crawler;

/**
 * They seem to cross-post some recipes to multiple sites - canonical may point to an
 * entirely different domain.
 *
 * Has LD+JSON but it is malformed on some pages.
 *
 * Some recipes have two recipeYield items.
 */
class HearstDigitalMedia extends SchemaOrgMarkup
{
    /**
     * @var string[]
     */
    protected $supportedHosts = [
        'www.countryliving.com',
        'www.delish.com',
        'www.esquire.com',
        'www.goodhousekeeping.com',
        'www.womansday.com',
    ];

    public function supports(Crawler $crawler) : bool
    {
        return parent::supports($crawler) && in_array(
            parse_url($crawler->getUri(), PHP_URL_HOST),
            $this->supportedHosts,
            true
        );
    }

    protected function extractAuthor(Crawler $crawler)
    {
        return $this->extractString($crawler, '[rel="author"]');
    }

    protected function extractCategories(Crawler $crawler)
    {
        return $this->extractArray($crawler, '.tags--top .tags--item');
    }

    protected function extractDescription(Crawler $crawler)
    {
        return $this->extractString($crawler, '[name="description"]', ['content']);
    }

    protected function extractImage(Crawler $crawler)
    {
        return $this->extractString($crawler, '[property="og:image"]', ['content']);
    }

    protected function extractIngredients(Crawler $crawler)
    {
        return $this->extractArray(
            $crawler,
            '[itemprop="ingredients"], .recipe-ingredients-group-header'
        );
    }

    protected function extractInstructions(Crawler $crawler)
    {
        return $this->extractArray($crawler, '[itemprop="recipeInstructions"] li');
    }

    protected function extractUrl(Crawler $crawler)
    {
        return $this->extractString($crawler, '[rel="canonical"]', ['href']);
    }
}
