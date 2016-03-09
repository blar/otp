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
     * @var DateInterval
     */
    private $interval = 30;

    /**
     * @return DateTimeInterface
     */
    public function getDateTime() {
        if(is_null($this->dateTime)) {
            return new DateTime();
        }
        return $this->dateTime;
    }

    /**
     * @param DateTimeInterface $dateTime
     * @return $this
     */
    public function setDateTime(DateTimeInterface $dateTime) {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getInterval() {
        return $this->interval;
    }

    /**
     * @param int $interval
     * @return $this
     */
    public function setInterval($interval) {
        $this->interval = $interval;
        return $this;
    }

    /**
     * @return string
     */
    public function getCounter() {
        return floor($this->getDateTime()->getTimestamp() / $this->getInterval());
    }

    /**
     * @param string $otp
     * @return bool
     */
    public function validate($otp) {
        $result = parent::validate($otp);
        return $result;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return [
            'issuer' => $this->getIssuer(),
            'algorithm' => $this->getAlgorithm(),
            'digits' => $this->getDigits(),
            'period' => $this->getInterval(),
        ];
    }

    /**
     * @return string
     */
    public function getType() {
        return 'totp';
    }

}
