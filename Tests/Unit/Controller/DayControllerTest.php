<?php

namespace JWeiland\Events2\Tests\Unit\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use JWeiland\Events2\Controller\DayController;
use JWeiland\Events2\Domain\Model\Day;
use JWeiland\Events2\Domain\Model\Event;
use JWeiland\Events2\Domain\Model\Filter;
use JWeiland\Events2\Domain\Repository\DayRepository;
use JWeiland\Events2\Domain\Repository\EventRepository;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Fluid\View\TemplateView;

/**
 * Test case.
 *
 * @author Stefan Froemken <projects@jweiland.net>
 */
class DayControllerTest extends UnitTestCase
{
    /**
     * @var DayController|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $subject;

    /**
     * @var DayRepository|ObjectProphecy
     */
    protected $dayRepository;

    /**
     * @var EventRepository|ObjectProphecy
     */
    protected $eventRepository;

    /**
     * @var ObjectManager|ObjectProphecy
     */
    protected $objectManager;

    /**
     * @var TemplateView|ObjectProphecy
     */
    protected $view;

    /**
     * set up.
     */
    public function setUp()
    {
        $this->objectManager = $this->prophesize(ObjectManager::class);
        $this->view = $this->prophesize(TemplateView::class);
        $this->dayRepository = $this->prophesize(DayRepository::class);
        $this->eventRepository = $this->prophesize(EventRepository::class);

        $settings = [
            'latest' => [
                'amountOfRecordsToShow' => 7
            ]
        ];

        $this->subject = $this->getAccessibleMock(DayController::class, ['dummy']);
        $this->subject->_set('settings', $settings);
        $this->subject->_set('objectManager', $this->objectManager->reveal());
        $this->subject->_set('view', $this->view->reveal());
        $this->subject->_set('dayRepository', $this->dayRepository->reveal());
        $this->subject->_set('eventRepository', $this->eventRepository->reveal());
    }

    /**
     * taer down.
     */
    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function listActionFindEventsAndAssignsThemToView()
    {
        $filter = new Filter();

        $this->dayRepository->findEvents(
            Argument::exact('list'),
            Argument::exact($filter)
        )->shouldBeCalled()->willReturn([]);
        $this->view->assign(
            Argument::exact('filter'),
            Argument::exact($filter)
        )->shouldBeCalled();
        $this->view->assign(
            Argument::exact('days'),
            Argument::exact([])
        )->shouldBeCalled();

        $this->subject->listAction($filter);
    }

    /**
     * @test
     */
    public function listLatestActionFindEventsAndAssignsThemToView()
    {
        $filter = new Filter();
        $queryResult = new QueryResult(new Query(Day::class));
        $settings = $this->subject->_get('settings');
        $settings['mergeEvents'] = 0;
        $this->subject->_set('settings', $settings);

        $this->dayRepository->findEvents(
            Argument::exact('latest'),
            Argument::exact($filter),
            Argument::exact(7)
        )->shouldBeCalled()->willReturn($queryResult);
        $this->dayRepository->groupDaysByEventAndSort(
            Argument::cetera()
        )->shouldNotBeCalled();
        $this->view->assign(
            Argument::exact('filter'),
            Argument::exact($filter)
        )->shouldBeCalled();
        $this->view->assign(
            Argument::exact('days'),
            Argument::exact($queryResult)
        )->shouldBeCalled();

        $this->subject->listLatestAction($filter);
    }

    /**
     * @test
     */
    public function listLatestActionFindEventsGroupAndSortAndAssignsThemToView()
    {
        $filter = new Filter();
        $queryResult = new QueryResult(new Query(Day::class));
        $settings = $this->subject->_get('settings');
        $settings['mergeEvents'] = 1;
        $this->subject->_set('settings', $settings);

        $this->dayRepository->findEvents(
            Argument::exact('latest'),
            Argument::exact($filter),
            Argument::exact(0)
        )->shouldBeCalled()->willReturn($queryResult);
        $this->dayRepository->groupDaysByEventAndSort(
            Argument::exact($queryResult),
            Argument::exact(7)
        )->shouldBeCalled()->willReturn([]);
        $this->view->assign(
            Argument::exact('filter'),
            Argument::exact($filter)
        )->shouldBeCalled();
        $this->view->assign(
            Argument::exact('days'),
            Argument::exact([])
        )->shouldBeCalled();

        $this->subject->listLatestAction($filter);
    }

    /**
     * @test
     */
    public function listTodayActionFindEventsAndAssignsThemToView()
    {
        $filter = new Filter();

        $this->dayRepository->findEvents(
            Argument::exact('today'),
            Argument::exact($filter)
        )->shouldBeCalled()->willReturn([]);
        $this->view->assign(
            Argument::exact('filter'),
            Argument::exact($filter)
        )->shouldBeCalled();
        $this->view->assign(
            Argument::exact('days'),
            Argument::exact([])
        )->shouldBeCalled();

        $this->subject->listTodayAction($filter);
    }

    /**
     * @test
     */
    public function listThisWeekActionFindEventsAndAssignsThemToView()
    {
        $filter = new Filter();

        $this->dayRepository->findEvents(
            Argument::exact('thisWeek'),
            Argument::exact($filter)
        )->shouldBeCalled()->willReturn([]);
        $this->view->assign(
            Argument::exact('filter'),
            Argument::exact($filter)
        )->shouldBeCalled();
        $this->view->assign(
            Argument::exact('days'),
            Argument::exact([])
        )->shouldBeCalled();

        $this->subject->listThisWeekAction($filter);

    }

    /**
     * @test
     */
    public function listRangeActionFindEventsAndAssignsThemToView()
    {
        $filter = new Filter();

        $this->dayRepository->findEvents(
            Argument::exact('range'),
            Argument::exact($filter)
        )->shouldBeCalled()->willReturn([]);
        $this->view->assign(
            Argument::exact('filter'),
            Argument::exact($filter)
        )->shouldBeCalled();
        $this->view->assign(
            Argument::exact('days'),
            Argument::exact([])
        )->shouldBeCalled();

        $this->subject->listRangeAction($filter);

    }

    /**
     * @test
     */
    public function showActionWithTimestampCallsAssign()
    {
        $event = 32415;
        $timestamp = 1234567890;
        $day = new Day();

        $this->dayRepository->findOneByTimestamp(
            Argument::exact($event),
            Argument::exact($timestamp)
        )->shouldBeCalled()->willReturn($day);
        $this->eventRepository
            ->findByIdentifier(Argument::cetera())
            ->shouldNotBeCalled();
        $this->view->assign(
            Argument::exact('day'),
            Argument::exact($day)
        )->shouldBeCalled();

        $this->subject->showAction($event, $timestamp);
    }

    /**
     * @test
     */
    public function showActionWithoutTimestampGeneratesEmptyDay()
    {
        $eventUid = 32415;
        $event = new Event();
        $day = new Day();
        $day->setEvent($event);

        $this->dayRepository
            ->findOneByTimestamp(
                Argument::exact($eventUid),
                Argument::exact(0)
            )
            ->shouldBeCalled()
            ->willReturn(null);
        $this->eventRepository
            ->findByIdentifier(Argument::exact($eventUid))
            ->shouldBeCalled()
            ->willReturn($event);
        $this->objectManager
            ->get(Day::class)
            ->shouldBeCalled()
            ->willReturn($day);
        $this->view->assign(
            Argument::exact('day'),
            Argument::exact($day)
        )->shouldBeCalled();

        $this->subject->showAction($eventUid);
    }

    /**
     * @test
     */
    public function showByTimestampWithTimestampCallsAssign()
    {
        $timestamp = 1234567890;

        $this->dayRepository
            ->findByTimestamp(Argument::exact($timestamp))
            ->shouldBeCalled()
            ->willReturn([]);

        $this->view->assign(
            Argument::exact('days'),
            Argument::exact([])
        );

        $this->subject->showByTimestampAction($timestamp);
    }

    /**
     * @test
     */
    public function showByTimestampWithInvalidTimestampCallsFindByTimestampWithZero()
    {
        $this->dayRepository
            ->findByTimestamp(Argument::exact(0))
            ->shouldBeCalled()
            ->willReturn([]);

        $this->view->assign(
            Argument::exact('days'),
            Argument::exact([])
        );

        $this->subject->showByTimestampAction('abc');
    }
}
