<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/events2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Events2\ViewHelpers;

use JWeiland\Events2\Domain\Factory\TimeFactory;
use JWeiland\Events2\Domain\Model\Event;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns sorted time records for a given event and date
 */
final class GetMergedEventTimesViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'event',
            Event::class,
            'The event to get the times from',
            true,
        );
        $this->registerArgument(
            'date',
            \DateTimeImmutable::class,
            'The date to get the times from',
            true,
        );
    }

    /**
     * One event can have until 4 relations to time records.
     * This ViewHelpers helps you find the times with the highest priority and merge them into one collection.
     */
    public function render(): \SplObjectStorage
    {
        $timeFactory = GeneralUtility::makeInstance(TimeFactory::class);

        return $timeFactory->getSortedTimesForDate(
            $this->arguments['event'],
            $this->arguments['date'],
        );
    }
}
