<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;

class TotpTest extends TestCase {

    public function testGetUrl() {
        $otp = new Totp();
        $otp->setAlgorithm('MD5');
        $otp->setInterval(60);
        $otp->setDigits(6);
        $otp->setSecret('1337');
        $this->assertSame('otpauth://totp/?algorithm=MD5&digits=6&period=60&secret=GEZTGNY', $otp->getUrl());
    }

    public function testGetUrl2() {
        $otp = new Totp();
        $otp->setAlgorithm('SHA1');
        $otp->setInterval(30);
        $otp->setDigits(6);
        $otp->setSecret('foobar');
        $this->assertSame('otpauth://totp/?algorithm=SHA1&digits=6&period=30&secret=MZXW6YTBOI', $otp->getUrl());
    }

    public function testSha1() {
        $otp = new Totp();
        $otp->setAlgorithm('SHA1');
        $otp->setInterval(30);
        $otp->setDigits(6);
        $otp->setSecret('foobar');

        date_default_timezone_set('Europe/Berlin');

        $otp->setDateTime(new DateTime('2015-01-01 13:37:00'));
        $this->assertSame('280242', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:37:30'));
        $this->assertSame('905682', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:38:00'));
        $this->assertSame('400409', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:38:30'));
        $this->assertSame('730069', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:39:00'));
        $this->assertSame('947232', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:39:30'));
        $this->assertSame('672341', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:40:00'));
        $this->assertSame('484493', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:40:30'));
        $this->assertSame('163499', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:41:00'));
        $this->assertSame('592029', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:41:30'));
        $this->assertSame('739650', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:42:00'));
        $this->assertSame('546616', $otp->generate());
    }

    public function testSha1With() {
        $otp = new Totp();
        $otp->setAlgorithm('SHA1');
        $otp->setInterval(30);
        $otp->setDigits(6);
        $otp->setSecret('foobar');

        date_default_timezone_set('Europe/Berlin');

        $otp->setDateTime(new DateTime('2015-01-01 13:37:01'));
        $this->assertSame('280242', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:37:32'));
        $this->assertSame('905682', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:38:03'));
        $this->assertSame('400409', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:38:34'));
        $this->assertSame('730069', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:39:05'));
        $this->assertSame('947232', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:39:36'));
        $this->assertSame('672341', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:40:07'));
        $this->assertSame('484493', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:40:38'));
        $this->assertSame('163499', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:41:09'));
        $this->assertSame('592029', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:41:31'));
        $this->assertSame('739650', $otp->generate());

        $otp->setDateTime(new DateTime('2015-01-01 13:42:00'));
        $this->assertSame('546616', $otp->generate());
    }


    public function testTotpRfcWithSha1() {
        $otp = new Totp();
        $otp->setInterval(30);
        $otp->setDigits(8);
        $otp->setSecret('12345678901234567890');

        // The time presented in the test vector has to be first divided through 30
        // to count as the key
        // SHA 1 grouping

        $otp->setAlgorithm('SHA1');

        $otp->setDateTime(new DateTime('@59'));
        $this->assertSame('94287082', $otp->generate(), 'sha1 with time 59');

        $otp->setDateTime(new DateTime('@1111111109'));
        $this->assertSame('07081804', $otp->generate(), 'sha1 with time 1111111109');

        $otp->setDateTime(new DateTime('@1111111111'));
        $this->assertSame('14050471', $otp->generate(), 'sha1 with time 1111111111');

        $otp->setDateTime(new DateTime('@1234567890'));
        $this->assertSame('89005924', $otp->generate(), 'sha1 with time 1234567890');

        $otp->setDateTime(new DateTime('@2000000000'));
        $this->assertSame('69279037', $otp->generate(), 'sha1 with time 2000000000');

        $otp->setDateTime(new DateTime('@20000000000'));
        $this->assertSame('65353130', $otp->generate(), 'sha1 with time 20000000000');
    }

    public function testSha256() {
        $this->markTestSkipped('No support for SHA-256');
        $otp = new Totp();
        $otp->setInterval(30);
        $otp->setDigits(8);
        $otp->setSecret('12345678901234567890');

        // SHA 256 grouping

        $otp->setAlgorithm('SHA256');

        date_default_timezone_set('UTC');
        $otp->setDateTime(new DateTime('@0'));
        $this->assertEquals('46119246', $otp->generate(), 'sha256 with time 59');

        /*
        $this->Otp->setAlgorithm('sha256');
        $this->assertEquals('46119246', $this->Otp->hotp($secret,          floor(59/30)), 'sha256 with time 59');
        $this->assertEquals('07081804', $this->Otp->hotp($secret,  floor(1111111109/30)), 'sha256 with time 1111111109');
        $this->assertEquals('14050471', $this->Otp->hotp($secret,  floor(1111111111/30)), 'sha256 with time 1111111111');
        $this->assertEquals('89005924', $this->Otp->hotp($secret,  floor(1234567890/30)), 'sha256 with time 1234567890');
        $this->assertEquals('69279037', $this->Otp->hotp($secret,  floor(2000000000/30)), 'sha256 with time 2000000000');
        $this->assertEquals('65353130', $this->Otp->hotp($secret, floor(20000000000/30)), 'sha256 with time 20000000000');

        */
    }

    public function testSha512() {
        $this->markTestSkipped('No support for SHA-512');

        $otp = new Totp();
        $otp->setInterval(30);
        $otp->setDigits(8);
        $otp->setSecret('12345678901234567890');

        $otp->setAlgorithm('SHA512');

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
