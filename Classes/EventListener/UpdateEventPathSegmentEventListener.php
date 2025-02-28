<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/events2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Events2\EventListener;

use JWeiland\Events2\Domain\Model\Event;
use JWeiland\Events2\Domain\Repository\EventRepository;
use JWeiland\Events2\Event\PostProcessControllerActionEvent;
use JWeiland\Events2\Helper\PathSegmentHelper;
use JWeiland\Events2\Traits\IsValidEventListenerRequestTrait;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Update path_segment of event.
 * Please check, if this EventListener was loaded before other redirecting EventListeners.
 */
#[AsEventListener('events2/updateEventPathSegment')]
final readonly class UpdateEventPathSegmentEventListener
{
    use IsValidEventListenerRequestTrait;

    /**
     * It should never be possible for a FE user to generate slug while update request. This would also change the
     * link to the detail page. If it was needed to change the link, please update slug in TYPO3 backend.
     */
    protected const ALLOWED_CONTROLLER_ACTIONS = [
        'Management' => [
            'create',
        ],
    ];

    public function __construct(
        private PathSegmentHelper $pathSegmentHelper,
        private EventRepository $eventRepository,
        private PersistenceManagerInterface $persistenceManager,
    ) {}

    public function __invoke(PostProcessControllerActionEvent $controllerActionEvent): void
    {
        if (
            $this->isValidRequest($controllerActionEvent)
            && $controllerActionEvent->getEvent() instanceof Event
        ) {
            $this->pathSegmentHelper->updatePathSegmentForEvent($controllerActionEvent->getEvent());
            $pathSegment = $controllerActionEvent->getEvent()->getPathSegment();

            if ($pathSegment === '' || $pathSegment === '/') {
                throw new \Exception(
                    'Path Segment of event is empty. Please check pathSegmentType in Extension Settings',
                    1611157656,
                );
            }
        }
    }
}
