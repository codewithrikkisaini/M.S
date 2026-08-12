<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Document Types Configuration
    |--------------------------------------------------------------------------
    |
    | Define all supported hotel document types. Each type specifies:
    |   - key:           Unique internal identifier (snake_case)
    |   - name:          Human-readable display name
    |   - description:   Short description shown to hotel admins
    |   - required:      Whether this document is mandatory
    |   - allowed_mimes: Accepted MIME types
    |   - max_size_mb:   Maximum file size in megabytes
    |
    */

    'types' => [

        'business_registration' => [
            'key'            => 'business_registration',
            'name'           => 'Business Registration Certificate',
            'description'    => 'Official business registration or incorporation certificate.',
            'required'       => true,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'gst_certificate' => [
            'key'            => 'gst_certificate',
            'name'           => 'GST Certificate',
            'description'    => 'Goods and Services Tax registration certificate (India).',
            'required'       => false,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'vat_certificate' => [
            'key'            => 'vat_certificate',
            'name'           => 'VAT Certificate',
            'description'    => 'Value Added Tax registration certificate.',
            'required'       => false,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'ein_letter' => [
            'key'            => 'ein_letter',
            'name'           => 'EIN Letter',
            'description'    => 'Employer Identification Number (EIN) confirmation letter from the IRS (USA).',
            'required'       => false,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'tax_registration' => [
            'key'            => 'tax_registration',
            'name'           => 'Tax Registration',
            'description'    => 'General tax registration or tax compliance certificate.',
            'required'       => false,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'owner_passport' => [
            'key'            => 'owner_passport',
            'name'           => 'Owner Passport',
            'description'    => 'Passport of the hotel owner or authorized representative.',
            'required'       => true,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'government_id' => [
            'key'            => 'government_id',
            'name'           => 'Government ID',
            'description'    => 'Government-issued photo identification (national ID, driver license, etc.).',
            'required'       => true,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'hotel_license' => [
            'key'            => 'hotel_license',
            'name'           => 'Hotel License',
            'description'    => 'Operating license or permit for the hotel establishment.',
            'required'       => true,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'fire_safety_certificate' => [
            'key'            => 'fire_safety_certificate',
            'name'           => 'Fire Safety Certificate',
            'description'    => 'Fire safety compliance certificate or inspection report.',
            'required'       => false,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

        'insurance_certificate' => [
            'key'            => 'insurance_certificate',
            'name'           => 'Insurance Certificate',
            'description'    => 'Property or liability insurance certificate.',
            'required'       => false,
            'allowed_mimes'  => ['application/pdf', 'image/jpeg', 'image/png'],
            'max_size_mb'    => 20,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Country-Specific Requirements
    |--------------------------------------------------------------------------
    |
    | Override default required status based on hotel country.
    | Keys are lowercase country names; values are arrays of document_type
    | keys that become required for that country.
    |
    */

    'country_requirements' => [

        'india' => [
            'business_registration',
            'gst_certificate',
            'owner_passport',
            'government_id',
            'hotel_license',
        ],

        'united states' => [
            'business_registration',
            'ein_letter',
            'government_id',
            'hotel_license',
        ],

        'united kingdom' => [
            'business_registration',
            'government_id',
            'hotel_license',
        ],

        'canada' => [
            'business_registration',
            'government_id',
            'hotel_license',
        ],

        'australia' => [
            'business_registration',
            'government_id',
            'hotel_license',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    */

    'allowed_mimes' => ['application/pdf', 'image/jpeg', 'image/png'],

    /*
    |--------------------------------------------------------------------------
    | Max File Size (MB)
    |--------------------------------------------------------------------------
    */

    'max_size_mb' => 20,

];
