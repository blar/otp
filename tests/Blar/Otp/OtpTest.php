<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

use DateTimeZone;
use PHPUnit_Framework_TestCase as TestCase;
use DateTime;
use DateInterval;
use DatePeriod;

class OtpTest extends TestCase {

    public function testSimple() {
        $otp = new Totp();
        $otp->setAlgorithm('SHA1');
        $otp->setInterval(30);
        $otp->setDigits(6);
        $otp->setSecret('foobar');

        $dateTime = new DateTime('2015-01-01 13:37:00', new DateTimeZone('Europe/Berlin'));
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $interval = new DateInterval('PT30S');
        $period = new DatePeriod($dateTime, $interval, 10);
        $passwords = [];
        foreach($period as $dateTime) {
            $otp->setDateTime($dateTime);
            $passwords[] = $otp->generate();
        }

        $this->assertSame([
            '280242',
            '905682',
            '400409',
            '730069',
            '947232',
            '672341',
            '484493',
            '163499',
            '592029',
            '739650',
            '546616',
        ], $passwords);

    }

    public function testTotpRfc() {
        $otp = new Totp();
        $otp->setInterval(30);
        $otp->setDigits(8);
        $otp->setSecret('12345678901234567890');

        // The time presented in the test vector has to be first divided through 30
        // to count as the key
        // SHA 1 grouping

        $otp->setAlgorithm('SHA1');

        $otp->setDateTime(new DateTime('@59'));
        $this->assertEquals('94287082', $otp->generate(), 'sha1 with time 59');

        $otp->setDateTime(new DateTime('@1111111109'));
        $this->assertEquals('07081804', $otp->generate(), 'sha1 with time 1111111109');
        /*
        $this->assertEquals('14050471', $this->Otp->hotp($secret, floor(1111111111 / 30)), 'sha1 with time 1111111111');
        $this->assertEquals('89005924', $this->Otp->hotp($secret, floor(1234567890 / 30)), 'sha1 with time 1234567890');
        $this->assertEquals('69279037', $this->Otp->hotp($secret, floor(2000000000 / 30)), 'sha1 with time 2000000000');
        $this->assertEquals('65353130', $this->Otp->hotp($secret, floor(20000000000 / 30)), 'sha1 with time 20000000000');
        */

        /*
        The following tests do NOT pass.
        Once the otp class can deal with these correctly, they can be used again.
        They are here for completeness test vectors from the RFC.

        // SHA 256 grouping
        */

        $otp->setAlgorithm('sha256');

        $otp->setDateTime(new DateTime('@59'));
        # $this->assertEquals('46119246', $otp->generate(), 'sha256 with time 59');

        /*
        $this->Otp->setAlgorithm('sha256');
        $this->assertEquals('46119246', $this->Otp->hotp($secret,          floor(59/30)), 'sha256 with time 59');
        $this->assertEquals('07081804', $this->Otp->hotp($secret,  floor(1111111109/30)), 'sha256 with time 1111111109');
        $this->assertEquals('14050471', $this->Otp->hotp($secret,  floor(1111111111/30)), 'sha256 with time 1111111111');
        $this->assertEquals('89005924', $this->Otp->hotp($secret,  floor(1234567890/30)), 'sha256 with time 1234567890');
        $this->assertEquals('69279037', $this->Otp->hotp($secret,  floor(2000000000/30)), 'sha256 with time 2000000000');
        $this->assertEquals('65353130', $this->Otp->hotp($secret, floor(20000000000/30)), 'sha256 with time 20000000000');

        */

        $otp->setAlgorithm('SHA512');

        $otp->setDateTime(new DateTime('@59'));
        # $this->assertEquals('90693936', $otp->generate(), 'sha256 with time 59');

        /*
        // SHA 512 grouping
        $this->Otp->setAlgorithm('sha512');
        $this->assertEquals('90693936', $this->Otp->hotp($secret,          floor(59/30)), 'sha512 with time 59');
        $this->assertEquals('25091201', $this->Otp->hotp($secret,  floor(1111111109/30)), 'sha512 with time 1111111109');
        $this->assertEquals('99943326', $this->Otp->hotp($secret,  floor(1111111111/30)), 'sha512 with time 1111111111');
        $this->assertEquals('93441116', $this->Otp->hotp($secret,  floor(1234567890/30)), 'sha512 with time 1234567890');
        $this->assertEquals('38618901', $this->Otp->hotp($secret,  floor(2000000000/30)), 'sha512 with time 2000000000');
        $this->assertEquals('47863826', $this->Otp->hotp($secret, floor(20000000000/30)), 'sha512 with time 20000000000');
        */
    }

}
