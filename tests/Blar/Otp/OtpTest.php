<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

use Base32\Base32;
use Blar\GoogleCharts\Qrcode;
use PHPUnit_Framework_TestCase as TestCase;

class OtpTest extends TestCase {

    public function testSimple() {
        return false;
        $otp = new Totp();
        $otp->setLabel('username@example.com');
        $otp->setIssuer('example.com');
        $otp->setAlgorithm('SHA512');
        $otp->setInterval(30);
        $otp->setDigits(6);

        $secret = $otp->createSecret();
        $otp->setSecret($secret);

        printf("Token: %s\n", Base32::encode($secret));
        printf("Token-Url: %s\n", $otp->getUrl());
        $qrcode = new Qrcode();
        printf("Qrcode-Url: %s\n", $qrcode->createUrl($otp->getUrl()));

        for($i=0; $i<10; $i++) {
            ob_flush();
            printf("Token: %s\n", $otp->generate());
            sleep(10);
        }

    }

}
