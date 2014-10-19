<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

use Base32\Base32;

/**
 * Class Otp
 *
 * @package Blar\Otp
 */
abstract class Otp {

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $issuer;

    /**
     * @var int
     */
    protected $digits = 6;

    /**
     * @var string
     */
    protected $algorithm = 'SHA1';

    public static function createSecret($prefix = NULL) {
        return sha1(uniqid($prefix, TRUE), TRUE);
    }

    /**
     * @return mixed
     */
    public function getSecret() {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     * @return $this
     */
    public function setSecret($secret) {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getIssuer() {
        return $this->issuer;
    }

    /**
     * @param string $issuer
     * @return $this
     */
    public function setIssuer($issuer) {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @return int
     */
    public function getDigits() {
        return $this->digits;
    }

    /**
     * @param int $digits
     * @return $this
     */
    public function setDigits($digits) {
        $this->digits = $digits;
        return $this;
    }

    /**
     * @param string $algorithm
     * @return $this
     */
    public function setAlgorithm($algorithm) {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlgorithm() {
        return $this->algorithm;
    }

    /**
     * @return string
     */
    public function generate() {
        $hash = hash_hmac(
            $this->getAlgorithm(),
            $this->getFormattedCounter(),
            $this->getSecret(),
            true
        );
        $otp = $this->truncateHash($hash);
        return $this->format($otp);
    }

    /**
     * @param string $otp
     * @return bool
     */
    public function validate($otp) {
        return $this->generate() == $otp;
    }

    /**
     * @param string $hash
     * @return int
     */
    public function truncateHash($hash) {
        $offset = ord(substr($hash, -1)) & 0x0F;
        $part = substr($hash, $offset, 4);
        $values = unpack('N', $part);
        $value = array_shift($values);
        // Only 32 bits
        return $value & 0x7FFFFFFF;
    }

    /**
     * @return string
     */
    abstract public function getCounter();

    /**
     * @return array
     */
    abstract public function getOptions();

    /**
     * @param string $otp
     * @return string
     */
    public function format($otp) {
        if($this->getDigits() < 10) {
            $otp %= pow(10, $this->getDigits());
        }
        return str_pad($otp, $this->getDigits(), '0', STR_PAD_LEFT);
    }

    /**
     * @param string $secret
     * @param array $options
     * @return string
     */
    public function getUrl() {
        $options = $this->getOptions();
        $options['secret'] = Base32::encode($this->getSecret());
        return sprintf('otpauth://%s/%s?%s', $this->getType(), $this->getLabel(), http_build_query($options));
    }

}
