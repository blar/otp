<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

use DateTimeInterface;
use DateTime;
use DateInterval;

/**
 * Class Totp
 *
 * @package Blar\Otp
 */
class Totp extends Hotp {

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var int
     */
    private $interval = 30;

    /**
     * @return DateTimeInterface
     */
    public function getDateTime(): DateTimeInterface {
        if(is_null($this->dateTime)) {
            return new DateTime();
        }
        return $this->dateTime;
    }

    /**
     * @param DateTimeInterface $dateTime
     */
    public function setDateTime(DateTimeInterface $dateTime) {
        $this->dateTime = $dateTime;
    }

    /**
     * @return int
     */
    public function getInterval() {
        return $this->interval;
    }

    /**
     * @param int $interval
     */
    public function setInterval(int $interval) {
        $this->interval = $interval;
    }

    /**
     * @return int
     */
    public function getCounter(): int {
        return floor($this->getDateTime()->getTimestamp() / $this->getInterval());
    }

    /**
     * @param string $otp
     * @return bool
     */
    public function validate(string $otp): bool {
        return parent::validate($otp);
    }

    /**
     * @return array
     */
    public function getOptions(): array {
        $options = [
            'algorithm' => $this->getAlgorithm(),
            'digits' => $this->getDigits(),
            'period' => $this->getInterval(),
        ];
        if($this->hasIssuer()) {
            $options['issuer'] = $this->getIssuer();
        }
        return $options;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return 'totp';
    }

}
