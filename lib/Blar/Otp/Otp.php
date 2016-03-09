<?php

/**
 * @author Andreas Treichel <gmblar+github@gmail.com>
 */

namespace Blar\Otp;

use Base32\Base32;
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
     * @return bool
     */
    public function hasIssuer(): bool {
        return !is_null($this->issuer);
    }

    /**
     * @return string
     */
    public function getIssuer(): string {
        return $this->issuer;
    }

    /**
     * @param string $issuer
     */
    public function setIssuer(string $issuer) {
        $this->issuer = $issuer;
    }

    /**
     * @param string $otp
     *
     * @return bool
     */
    public function validate(string $otp): bool {
        return $this->generate() == $otp;
    }

    /**
     * @return string
     */
    public function generate(): string {
        $hash = $this->generateHash($this->getFormattedCounter());
        $otp = $this->truncateHash($hash);
        return $this->format($otp);
    }

    /**
     * @param $counter
     *
     * @return string
     */
    protected function generateHash(string $counter): string {
        $generator = new HmacHashGenerator($this->getAlgorithm(), $this->getSecret());
        $hash = $generator->hash($counter);
        $value = $hash->getValue();
        return $value;
    }

    /**
     * @return string
     */
    public function getAlgorithm(): string {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     */
    public function setAlgorithm(string $algorithm) {
        $this->algorithm = $algorithm;
    }

    /**
     * @return string
     */
    public function getSecret(): string {
        if(is_null($this->secret)) {
            $this->secret = static::createSecret();
        }
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret) {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    protected static function createSecret() {
        return bin2hex(random_bytes(8));
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
    protected function format(string $otp): string {
        if($this->getDigits() < 10) {
            $otp %= pow(10, $this->getDigits());
        }
        return str_pad($otp, $this->getDigits(), '0', STR_PAD_LEFT);
    }

    /**
     * @return int
     */
    public function getDigits(): int {
        return $this->digits;
    }

    /**
     * @param int $digits
     */
    public function setDigits(int $digits) {
        $this->digits = $digits;
    }

    /**
     * @return int
     */
    abstract public function getCounter(): int;

    /**
     * @return string
     */
    public function getUrl(): string {
        $options = $this->getOptions();
        $options['secret'] = trim(Base32::encode($this->getSecret()), '=');
        return sprintf(
            'otpauth://%s/%s?%s',
            $this->getType(),
            $this->hasLabel() ? $this->getLabel() : '',
            http_build_query($options)
        );
    }

    /**
     * @return array
     */
    abstract public function getOptions(): array;

    /**
     * @return bool
     */
    public function hasLabel(): bool {
        return !is_null($this->label);
    }

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label) {
        $this->label = $label;
    }

}
