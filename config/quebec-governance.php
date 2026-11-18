<?php

return [

    'table' => 'quebec_processing_activities',

    /*
     * The business, as it appears in the privacy policy.
     */
    'business' => [
        'name' => env('QUEBEC_BUSINESS_NAME', env('APP_NAME')),
        'address' => env('QUEBEC_BUSINESS_ADDRESS'),
        'contact' => env('QUEBEC_BUSINESS_CONTACT'),
    ],

    /*
     * The person in charge of the protection of personal information (s. 3.1). By default the person
     * with the highest authority; the function can be delegated in writing. Publish their title and
     * contact information on your website.
     */
    'person_in_charge' => [
        'name' => env('QUEBEC_PERSON_IN_CHARGE_NAME'),
        'title' => env('QUEBEC_PERSON_IN_CHARGE_TITLE'),
        'email' => env('QUEBEC_PERSON_IN_CHARGE_EMAIL'),
        'phone' => env('QUEBEC_PERSON_IN_CHARGE_PHONE'),
    ],

    /*
     * Governance policies and practices (s. 3.2): roles and responsibilities of personnel, keeping and
     * destruction of information, and the complaint process. Publish detailed information about them
     * in clear and simple terms. Each entry: ['name' => ..., 'url' => ...].
     */
    'policies' => [],

    /*
     * The authority persons can apply to.
     */
    'authority' => [
        'name' => "Commission d'accès à l'information du Québec",
        'url' => 'https://www.cai.gouv.qc.ca',
    ],

];
