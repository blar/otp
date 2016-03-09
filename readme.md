[![License](https://poser.pugx.org/blar/otp/license)](https://packagist.org/packages/blar/otp)
[![Latest Stable Version](https://poser.pugx.org/blar/otp/v/stable)](https://packagist.org/packages/blar/otp)
[![Build Status](https://travis-ci.org/blar/otp.svg?branch=master)](https://travis-ci.org/blar/otp)
[![Coverage Status](https://coveralls.io/repos/blar/otp/badge.svg?branch=master)](https://coveralls.io/r/blar/otp?branch=master)
[![Dependency Status](https://gemnasium.com/blar/otp.svg)](https://gemnasium.com/blar/otp)
[![Flattr](https://button.flattr.com/flattr-badge-large.png)](https://flattr.com/submit/auto?user_id=Blar&url=https%3A%2F%2Fgithub.com%2Fblar%2Fotp)

# One Time Password (OTP)

## „Time based“ One Time Password erstellen

### Setup

    $otp = new Totp();
    $otp->setLabel('username@example.com');
    $otp->setIssuer('example.com');

### Secret erstellen

Das Secret muss nur einmal pro Benutzer erstellt werden.

    $secret = $otp->createSecret();

### Secret zuweisen

    $otp->setSecret($secret);

### OTP-URL ausgeben

    $otp->getUrl();

Die Url sieht dann z.B. so aus:

    otpauth://totp/username@example.com?
        issuer = example.com
        algorithm = SHA1
        digits = 6
        period = 30
        secret = LWZU3NR3PN5FXUX6XTHWE7OIWJEAFTWC

### OTP-URL als QRCode ausgeben

Einige Authenticator unterstützen die Übertragung der Einstellungen durch das Scannen eines QRCodes. So ein QRCode kann
z.B. mit dem Paket [blar/google-charts](https://github.com/blar/google-charts/) erstellt werden.

    $qrcode = new Qrcode();
    $qrcode->setSize(256, 256);
    $qrcodeUrl = $qrcode->createUrl($otp->getUrl());

### Passwort prüfen

    $otp->validate($_POST['otp']);

## Google-Authenticator

Für den Google-Authenticator müssen folgende Einstellungen vorgenommen werden, da dieser für einige Einstellungen
Standardwerte verwendet die nicht über die otpauth-URL geändert werden können.

    $otp->setAlgorithm('SHA1');
    $otp->setDigits(6);
    $otp->setInterval(30);
