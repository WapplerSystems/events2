<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/events2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Events2\Tests\Unit\Service;

use JWeiland\Events2\Domain\Model\Event;
use JWeiland\Events2\Service\CacheService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class CacheServiceTest extends UnitTestCase
{
    protected CacheService $subject;

    protected ServerRequestInterface $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new ServerRequest('https://www.example.com', 'GET');
        $this->request = $this->request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $this->subject = new CacheService();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
            $this->request,
            $GLOBALS['TYPO3_REQUEST'],
        );

        parent::tearDown();
    }

    #[Test]
    public function addCacheTagsByEventRecordsWithoutEventsWillNotAddCacheTags(): void
    {
        $typoScriptFrontendControllerMock = $this->createMock(TypoScriptFrontendController::class);
        $typoScriptFrontendControllerMock
            ->expects(self::never())
            ->method('addCacheTags');

        $GLOBALS['TYPO3_REQUEST'] = $this->request->withAttribute(
            'frontend.controller',
            $typoScriptFrontendControllerMock,
        );

        $this->subject->addCacheTagsByEventRecords([]);
    }

    #[Test]
    public function addCacheTagsByEventRecordsWithEventsWillAddCacheTags(): void
    {
        $event = new Event();
        $event->_setProperty('uid', 123);

        $typoScriptFrontendControllerMock = $this->createMock(TypoScriptFrontendController::class);
        $typoScriptFrontendControllerMock
            ->expects(self::atLeastOnce())
            ->method('addCacheTags')
            ->with(['tx_events2_uid_123']);

        $GLOBALS['TYPO3_REQUEST'] = $this->request->withAttribute(
            'frontend.controller',
            $typoScriptFrontendControllerMock,
        );

        $this->subject->addCacheTagsByEventRecords([$event]);
    }

    #[Test]
    public function addCacheTagsByEventRecordsWithLocalizedEventsWillAddCacheTags(): void
    {
        $event = new Event();
        $event->_setProperty('uid', 123);
        $event->_setProperty('_localizedUid', 321);

        $typoScriptFrontendControllerMock = $this->createMock(TypoScriptFrontendController::class);
        $typoScriptFrontendControllerMock
            ->expects(self::atLeastOnce())
            ->method('addCacheTags')
            ->with(['tx_events2_uid_123', 'tx_events2_uid_321']);

        $GLOBALS['TYPO3_REQUEST'] = $this->request->withAttribute(
            'frontend.controller',
            $typoScriptFrontendControllerMock,
        );

        $this->subject->addCacheTagsByEventRecords([$event]);
    }

    #[Test]
    public function addPageCacheTagsByQueryWithoutStoragePidsWillAddTableNameAsCacheTag(): void
    {
        $typoScriptFrontendControllerMock = $this->createMock(TypoScriptFrontendController::class);
        $typoScriptFrontendControllerMock
            ->expects(self::atLeastOnce())
            ->method('addCacheTags')
            ->with(['tx_events2_domain_model_event']);

        $GLOBALS['TYPO3_REQUEST'] = $this->request->withAttribute(
            'frontend.controller',
            $typoScriptFrontendControllerMock,
        );

        /** @var QuerySettingsInterface|MockObject $querySettingsMock */
        $querySettingsMock = $this->createMock(Typo3QuerySettings::class);

        /** @var QueryInterface|MockObject $queryMock */
        $queryMock = $this->createMock(Query::class);
        $queryMock
            ->expects(self::atLeastOnce())
            ->method('getQuerySettings')
            ->willReturn($querySettingsMock);

        $this->subject->addPageCacheTagsByQuery($queryMock);
    }

    #[Test]
    public function addPageCacheTagsByQueryWithStoragePidsWillAddStoragePidCacheTags(): void
    {
        $typoScriptFrontendControllerMock = $this->createMock(TypoScriptFrontendController::class);
        $typoScriptFrontendControllerMock
            ->expects(self::atLeastOnce())
            ->method('addCacheTags')
            ->with(['tx_events2_pid_123', 'tx_events2_pid_234']);

        $GLOBALS['TYPO3_REQUEST'] = $this->request->withAttribute(
            'frontend.controller',
            $typoScriptFrontendControllerMock,
        );

        /** @var QuerySettingsInterface|MockObject $querySettingsMock */
        $querySettingsMock = $this->createMock(Typo3QuerySettings::class);
        $querySettingsMock
            ->expects(self::atLeastOnce())
            ->method('getStoragePageIds')
            ->willReturn([123, 234]);

        /** @var QueryInterface|MockObject $queryMock */
        $queryMock = $this->createMock(Query::class);
        $queryMock
            ->expects(self::atLeastOnce())
            ->method('getQuerySettings')
            ->willReturn($querySettingsMock);

        $this->subject->addPageCacheTagsByQuery($queryMock);
    }
}
