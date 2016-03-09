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
    public function getCounter(): int {
        return $this->counter;
    }

    /**
     * @param int $counter
     */
    public function setCounter(int $counter) {
        $this->counter = $counter;
    }

    /**
     * @return string
     */
    public function getFormattedCounter(): string {
        return pack('@4N*', $this->getCounter());
    }

    /**
     * @return array
     */
    public function getOptions(): array {
        $options = [
            'algorithm' => $this->getAlgorithm(),
            'digits' => $this->getDigits(),
            'counter' => $this->getFormattedCounter()
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
        return 'hotp';
    }

}
