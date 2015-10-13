<?php

namespace Gitiki\Twig;

use Symfony\Component\Translation\TranslatorInterface;

class CoreExtension extends \Twig_Extension
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('bytes_to_human', [$this, 'bytesToHuman']),
            new \Twig_SimpleFilter('date_day', [$this, 'dateDay']),
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

    public function dateDay(\DateTime $date)
    {
        $formatter = new \IntlDateFormatter($this->translator->getLocale(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);

        return $formatter->format($date);
    }

    public function getName()
    {
        return 'gitiki_core';
    }
}
