<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/events2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Events2\Tests\Functional\Configuration;

use JWeiland\Events2\Configuration\ExtConf;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
class ExtConfTest extends FunctionalTestCase
{
    protected ExtConf $subject;

    protected array $coreExtensionsToLoad = [
        'extensionmanager',
        'reactions',
    ];

    protected array $testExtensionsToLoad = [
        'sjbr/static-info-tables',
        'jweiland/events2',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ExtConf(new ExtensionConfiguration());
        $this->subject->setRecurringPast('3');
        $this->subject->setRecurringFuture('6');
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function getPoiCollectionPidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getPoiCollectionPid(),
        );
    }

    #[Test]
    public function setPoiCollectionPidSetsPoiCollectionPid(): void
    {
        $this->subject->setPoiCollectionPid(123456);

        self::assertSame(
            123456,
            $this->subject->getPoiCollectionPid(),
        );
    }

    #[Test]
    public function setPoiCollectionPidWithStringResultsInInteger(): void
    {
        $this->subject->setPoiCollectionPid('123Test');

        self::assertSame(
            123,
            $this->subject->getPoiCollectionPid(),
        );
    }

    #[Test]
    public function setPoiCollectionPidWithBooleanResultsInInteger(): void
    {
        $this->subject->setPoiCollectionPid(true);

        self::assertSame(
            1,
            $this->subject->getPoiCollectionPid(),
        );
    }

    #[Test]
    public function getRootUidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getRootUid(),
        );
    }

    #[Test]
    public function setRootUidSetsRootUid(): void
    {
        $this->subject->setRootUid(123456);

        self::assertSame(
            123456,
            $this->subject->getRootUid(),
        );
    }

    #[Test]
    public function setRootUidWithStringResultsInInteger(): void
    {
        $this->subject->setRootUid('123Test');

        self::assertSame(
            123,
            $this->subject->getRootUid(),
        );
    }

    #[Test]
    public function setRootUidWithBooleanResultsInInteger(): void
    {
        $this->subject->setRootUid(true);

        self::assertSame(
            1,
            $this->subject->getRootUid(),
        );
    }

    #[Test]
    public function getRecurringPastReturns3monthAsDefault(): void
    {
        self::assertSame(
            3,
            $this->subject->getRecurringPast(),
        );
    }

    #[Test]
    public function setRecurringPastWithIntegerWillReturnSameInGetter(): void
    {
        $this->subject->setRecurringPast(6);
        self::assertSame(
            6,
            $this->subject->getRecurringPast(),
        );
    }

    #[Test]
    public function setRecurringPastWithStringWillReturnIntegerInGetter(): void
    {
        $this->subject->setRecurringPast('6');
        self::assertSame(
            6,
            $this->subject->getRecurringPast(),
        );
    }

    #[Test]
    public function setRecurringPastWithInvalidValueWillReturnIntCastedValueInGetter(): void
    {
        $this->subject->setRecurringPast('invalidValue');
        self::assertSame(
            0,
            $this->subject->getRecurringPast(),
        );
    }

    #[Test]
    public function getRecurringFutureReturns6monthAsDefault(): void
    {
        self::assertSame(
            6,
            $this->subject->getRecurringFuture(),
        );
    }

    #[Test]
    public function setRecurringFutureWithIntegerWillReturnSameInGetter(): void
    {
        $this->subject->setRecurringFuture(12);
        self::assertSame(
            12,
            $this->subject->getRecurringFuture(),
        );
    }

    #[Test]
    public function setRecurringFutureWithStringWillReturnIntegerInGetter(): void
    {
        $this->subject->setRecurringFuture('12');
        self::assertSame(
            12,
            $this->subject->getRecurringFuture(),
        );
    }

    #[Test]
    public function setRecurringFutureWithInvalidValueWillReturnDefaultValueInGetter(): void
    {
        $this->subject->setRecurringFuture('invalidValue');
        self::assertSame(
            6,
            $this->subject->getRecurringFuture(),
        );
    }

    #[Test]
    public function getDefaultCountryInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->subject->getDefaultCountry(),
        );
    }

    #[Test]
    public function setDefaultCountrySetsDefaultCountryAsInteger(): void
    {
        $this->subject->setDefaultCountry('45');

        self::assertSame(
            45,
            $this->subject->getDefaultCountry(),
        );
    }

    #[Test]
    public function setDefaultCountryWithEmptyStringSetsDefaultCountryToZero(): void
    {
        $this->subject->setDefaultCountry('');

        self::assertSame(
            0,
            $this->subject->getDefaultCountry(),
        );
    }

    #[Test]
    public function getXmlImportValidatorPathInitiallyReturnsDefaultXsdPath(): void
    {
        self::assertSame(
            'EXT:events2/Resources/Public/XmlImportValidator.xsd',
            $this->subject->getXmlImportValidatorPath(),
        );
    }

    #[Test]
    public function setXmlImportValidatorPathSetsXmlImportValidatorPath(): void
    {
        $this->subject->setXmlImportValidatorPath('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getXmlImportValidatorPath(),
        );
    }

    #[Test]
    public function getOrganizerIsRequiredInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->getOrganizerIsRequired(),
        );
    }

    #[Test]
    public function setOrganizerIsRequiredSetsOrganizerIsRequired(): void
    {
        $this->subject->setOrganizerIsRequired(true);
        self::assertTrue(
            $this->subject->getOrganizerIsRequired(),
        );
    }

    #[Test]
    public function setOrganizerIsRequiredWithStringReturnsTrue(): void
    {
        $this->subject->setOrganizerIsRequired('foo bar');
        self::assertTrue($this->subject->getOrganizerIsRequired());
    }

    #[Test]
    public function setOrganizerIsRequiredWithZeroReturnsFalse(): void
    {
        $this->subject->setOrganizerIsRequired(0);
        self::assertFalse($this->subject->getOrganizerIsRequired());
    }

    #[Test]
    public function getLocationIsRequiredInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->subject->getLocationIsRequired(),
        );
    }

    #[Test]
    public function setLocationIsRequiredSetsLocationIsRequired(): void
    {
        $this->subject->setLocationIsRequired(true);
        self::assertTrue(
            $this->subject->getLocationIsRequired(),
        );
    }

    #[Test]
    public function setLocationIsRequiredWithStringReturnsTrue(): void
    {
        $this->subject->setLocationIsRequired('foo bar');
        self::assertTrue($this->subject->getLocationIsRequired());
    }

    #[Test]
    public function setLocationIsRequiredWithZeroReturnsFalse(): void
    {
        $this->subject->setLocationIsRequired(0);
        self::assertFalse($this->subject->getLocationIsRequired());
    }

    #[Test]
    public function getEmailFromAddressInitiallyReturnsEmptyString(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1484823422);

        self::assertSame(
            '',
            $this->subject->getEmailFromAddress(),
        );
    }

    #[Test]
    public function setEmailFromAddressSetsEmailFromAddress(): void
    {
        $this->subject->setEmailFromAddress('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmailFromAddress(),
        );
    }

    #[Test]
    public function getEmailFromNameInitiallyReturnsEmptyString(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1484823661);

        self::assertSame(
            '',
            $this->subject->getEmailFromName(),
        );
    }

    #[Test]
    public function setEmailFromNameSetsEmailFromName(): void
    {
        $this->subject->setEmailFromName('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmailFromName(),
        );
    }

    #[Test]
    public function getEmailToAddressInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getEmailToAddress(),
        );
    }

    #[Test]
    public function setEmailToAddressSetsEmailToAddress(): void
    {
        $this->subject->setEmailToAddress('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmailToAddress(),
        );
    }

    #[Test]
    public function getEmailToNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getEmailToName(),
        );
    }

    #[Test]
    public function setEmailToNameSetsEmailToName(): void
    {
        $this->subject->setEmailToName('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmailToName(),
        );
    }
}
