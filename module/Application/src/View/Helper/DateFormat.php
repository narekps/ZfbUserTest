<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class DateFormat
 *
 * @package Application\View\Helper
 */
class DateFormat extends AbstractHelper
{
    /**
     * @param \DateTime $dateTime
     * @param string    $pattern
     *
     * @return string
     */
    public function __invoke(\DateTime $dateTime, string $pattern = 'dd MMMM YYYY')
    {
        $formatter = new \IntlDateFormatter('ru_RU', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);
        $formatter->setPattern($pattern);

        return $formatter->format($dateTime);
    }
}
