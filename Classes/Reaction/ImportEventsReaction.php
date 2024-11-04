<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/events2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Events2\Reaction;

use JWeiland\Events2\Importer\JsonImporter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Reactions\Model\ReactionInstruction;
use TYPO3\CMS\Reactions\Reaction\ReactionInterface;

/**
 * A reaction to import events2 records
 */
class ImportEventsReaction implements ReactionInterface
{
    public function __construct(
        private readonly JsonImporter $jsonImporter,
        private readonly ResponseFactory $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {}

    public static function getType(): string
    {
        return 'import-events2-records';
    }

    public static function getDescription(): string
    {
        return 'LLL:EXT:events2/Resources/Private/Language/locallang_db.xlf:sys_reaction.reaction_type.import_events2_records';
    }

    public static function getIconIdentifier(): string
    {
        return 'content-database';
    }

    public function react(ServerRequestInterface $request, array $payload, ReactionInstruction $reaction): ResponseInterface
    {
        $statusData = [];

        $storagePid = $this->getStoragePid($reaction);

        // Early return, if storage PID is 0
        if ($storagePid === 0) {
            $statusData['success'] = false;
            $statusData['error'] = 'Storage pid can not be empty';
            return $this->jsonResponse($statusData);
        }

        $statusData['success'] = $this->jsonImporter->import($payload);

        if ($statusData['success'] === false) {
            $statusData['error'] = 'Error while importing events';
        }

        return $this->jsonResponse($statusData);
    }

    protected function jsonResponse(array $data, int $statusCode = 200): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream((string)json_encode($data)));
    }

    protected function getStoragePid(ReactionInstruction $reaction): int
    {
        return (int)($reaction->toArray()['storage_pid'] ?? 0);
    }
}
