<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

/**
 * Class Hotp
 *
 * @package Blar\Otp
 */
class Hotp extends Otp {

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * @return int
     */
    public function getCounter() {
        return $this->counter;
    }

    /**
     * @param int $counter
     * @return $this
     */
    public function setCounter($counter) {
        $this->counter = $counter;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormattedCounter() {
        return pack('@4N*', $this->getCounter());
    }

    /**
     * @return array
     */
    public function getOptions() {
        return [
            'issuer' => $this->getIssuer(),
            'algorithm' => $this->getAlgorithm(),
            'digits' => $this->getDigits(),
            'counter' => $this->getFormattedCounter()
        ];
    }

    /**
     * @return string
     */
    public function getType() {
        return 'hotp';
    }

}
