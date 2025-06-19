<?php

namespace App\Services;

class VendorValidationService
{
    /**
     * Validate a vendor's data according to business rules.
     *
     * @param array $vendorData
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate(array $vendorData): array
    {
        $errors = [];

        if (empty($vendorData['name'])) {
            $errors[] = 'Vendor name is required.';
        }
        if (empty($vendorData['email']) || !filter_var($vendorData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if (!isset($vendorData['registration_number']) || strlen($vendorData['registration_number']) < 5) {
            $errors[] = 'Registration number must be at least 5 characters.';
        }
        // Add more business rule checks as needed...

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}