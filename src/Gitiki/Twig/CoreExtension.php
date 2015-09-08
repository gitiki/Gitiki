<?php

namespace Gitiki\Twig;

use Silex\Translator;

class CoreExtension extends \Twig_Extension
{
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('bytes_to_human', [$this, 'bytesToHuman']),
        ];
    }

    public function bytesToHuman($bytes, $precision = 2)
    {
        $suffixes = ['bytes', 'kB', 'MB', 'GB', 'TB'];

        $formatter = new \NumberFormatter(
            $this->translator->getLocale(),
            \NumberFormatter::PATTERN_DECIMAL,
            0 === $precision ? '#' : '.'.str_repeat('#', $precision)
        );

        $exp = floor(log($bytes, 1024));

        return $formatter->format($bytes / pow(1024, floor($exp))).' '.$this->translator->trans($suffixes[$exp]);
    }

    public function getName()
    {
        return 'gitiki_core';
    }
}
