<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

use Base32\Base32;
use Blar\Hash\HmacHash;
use Blar\Hash\HmacHashGenerator;

/**
 * Class Otp
 *
 * @package Blar\Otp
 */
abstract class Otp {

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $issuer;

    /**
     * @var int
     */
    private $digits = 6;

    /**
     * @var string
     */
    private $algorithm = 'SHA1';

    /**
     * @return string
     */
    public function getIssuer() {
        return $this->issuer;
    }

    /**
     * @param string $issuer
     *
     * @return $this
     */
    public function setIssuer($issuer) {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @param string $otp
     *
     * @return bool
     */
    public function validate($otp) {
        return $this->generate() == $otp;
    }

    /**
     * @return string
     */
    public function generate() {
        $hash = $this->generateHash($this->getFormattedCounter());
        $otp = $this->truncateHash($hash);
        return $this->format($otp);
    }

    /**
     * @param $counter
     *
     * @return string
     */
    protected function generateHash($counter) {
        $generator = new HmacHashGenerator($this->getAlgorithm(), $this->getSecret());
        $hash = $generator->hash($counter);
        return $hash->getValue();
    }

    /**
     * @return string
     */
    public function getAlgorithm() {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     *
     * @return $this
     */
    public function setAlgorithm($algorithm) {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret() {
        if(is_null($this->secret)) {
            $this->secret = static::createSecret();
        }
        return $this->secret;
    }

    /**
     * @param string $secret
     *
     * @return $this
     */
    public function setSecret($secret) {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public static function createSecret($prefix = NULL) {
        return sha1(uniqid($prefix, TRUE), TRUE);
    }

    /**
     * @param string $hash
     *
     * @return int
     */
    public function truncateHash($hash) {
        $offset = ord(substr($hash, -1)) & 0xF;
        $part = substr($hash, $offset, 4);
        $values = unpack('N', $part);
        $value = array_shift($values);
        // Only 32 bits
        return $value & 0x7FFFFFFF;
    }

    /**
     * @param string $otp
     *
     * @return string
     */
    public function format($otp) {
        if($this->getDigits() < 10) {
            $otp %= pow(10, $this->getDigits());
        }
        return str_pad($otp, $this->getDigits(), '0', STR_PAD_LEFT);
    }

    /**
     * @return int
     */
    public function getDigits() {
        return $this->digits;
    }

    /**
     * @param int $digits
     *
     * @return $this
     */
    public function setDigits($digits) {
        $this->digits = $digits;
        return $this;
    }

    /**
     * @return string
     */
    abstract public function getCounter();

    /**
     * @return string
     */
    public function getUrl() {
        $options = $this->getOptions();
        $options['secret'] = Base32::encode($this->getSecret());
        return sprintf(
            'otpauth://%s/%s?%s',
            $this->getType(),
            $this->getLabel(),
            http_build_query($options)
        );
    }

    /**
     * @return array
     */
    abstract public function getOptions();

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

}
