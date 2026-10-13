<?php

namespace App\Service;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class PhoneNumberService
{
    private PhoneNumberUtil $lib;

    public function __construct()
    {
        $this->lib = PhoneNumberUtil::getInstance();
    }

    public function parse(string $number, string $defaultRegion = 'FR'): ?string
    {
        try {
            $phone = $this->lib->parse($number, $defaultRegion);

            if (!$this->lib->isValidNumber($phone)) {
                return null;
            }

            // Retourne format international standard E.164
            return $this->lib->format($phone, PhoneNumberFormat::E164);

        } catch (NumberParseException $e) {
            return null;
        }
    }
}
