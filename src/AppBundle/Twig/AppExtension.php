<?php

namespace AppBundle\Twig;

use AppBundle\Utils\Markdown;
use Symfony\Component\Intl\Intl;

class AppExtension extends \Twig_Extension
{
    /**
     * @var Markdown
     */
    private $parser;

    /**
     * @var array
     */
    private $locales;

    public function __construct(Markdown $parser, $locales)
    {
        $this->parser = $parser;
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('md2html', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('locales', [$this, 'getLocales']),
        ];
    }

    /**
     * Transforms the given Markdown content into HTML content.
     *
     *  @param string $content
     *
     * @return string
     */
    public function markdownToHtml($content)
    {
        return $this->parser->toHtml($content);
    }

    /**
     * Takes the list of codes of the locales (languages) enabled in the
     * application and returns an array with the name of each locale written
     * in its own language (e.g. English, Français, Español, etc.).
     *
     * @return array
     */
    public function getLocales()
    {
        $localeCodes = explode('|', $this->locales);

        $locales = [];
        foreach ($localeCodes as $localeCode) {
            $locales[] = ['code' => $localeCode, 'name' => Intl::getLocaleBundle()->getLocaleName($localeCode, $localeCode)];
        }

        return $locales;
    }
}