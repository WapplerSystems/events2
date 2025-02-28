<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/events2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Events2\EventListener;

use JWeiland\Events2\Domain\Repository\UserRepository;
use JWeiland\Events2\Event\PreProcessControllerActionEvent;
use JWeiland\Events2\Traits\IsValidEventListenerRequestTrait;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/**
 * Add organizer to event record manually.
 *
 * As it is possible for a hacker to change the user/organizer in source code of event form (FE)
 * I prefer adding the organizer manually here in PHP
 */
#[AsEventListener('events2/addOrganizerToEventRecord')]
final readonly class AddOrganizerToEventRecordEventListener
{
    use IsValidEventListenerRequestTrait;

    protected const ALLOWED_CONTROLLER_ACTIONS = [
        'Management' => [
            'create',
        ],
    ];

    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function __invoke(PreProcessControllerActionEvent $controllerActionEvent): void
    {
        if (
            $this->isValidRequest($controllerActionEvent)
            && $controllerActionEvent->getRequest()->hasArgument('event')
            && ($eventRecord = $controllerActionEvent->getRequest()->getArgument('event'))
            && is_array($eventRecord)
            && ($organizerOfCurrentUser = $this->userRepository->getFieldFromUser('tx_events2_organizer'))
            && MathUtility::canBeInterpretedAsInteger($organizerOfCurrentUser)
        ) {
            // Per default, it is not allowed to add new arguments manually. So we have to register them.
            $pmc = $controllerActionEvent->getArguments()
                ->getArgument('event')
                ->getPropertyMappingConfiguration();

            // Allow mapping of organizers
            $pmc->allowProperties('organizers');

            // Allow creation of new organizers
            $pmc->forProperty('organizers.*')
                ->setTypeConverterOption(
                    PersistentObjectConverter::class,
                    PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
                    true,
                );
        }
    }
}
