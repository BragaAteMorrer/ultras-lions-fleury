<?php

namespace App\Form;

final class DonationFormData
{
    public ?string $amountChoice = null;

    public ?string $customAmount = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $email = null;

    public bool $anonymous = false;

    public ?string $message = null;

    public ?string $submissionToken = null;
}
