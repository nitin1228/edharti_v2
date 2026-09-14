<?php
return [
    'rgr_factor' => [
        'lndo' => [
            'residential' => 2.5,
            'commercial' => 5
        ],
        'circle' => [
            'residential' => 1,
            'commercial' => 2
        ]
    ],
    'ldo_logo_path' => 'https://upload.wikimedia.org/wikipedia/commons/8/84/Government_of_India_logo.svg',
    'conversion_calculation_rate' => 'circle', //lndo, circle
    'OTP_EXPIRY_TIME' => 10, // changed from 1 to 2  by Amita [27-02-2025]
    'unearned_increase_factor' => 0.25, //by Nitin 20/Nov/24
    'payment_type_id' => 0, //added by Nitin 10/Jan/2024 , to be used in payment data xml
    'paymentURL' => "https://bharatkosh.gov.in/BKePay",
    'paymentStatusURL' => "https://bharatkosh.gov.in/NTRPHome/GetStatusBK",
    // 'paymentStatusURL' => "https://bharatkosh.gov.in/GetStatusBK",
    'oldDemandByPropertyId' => 'https://bharatkosh.gov.in/BKePay',
    'propertyDocList' => 'https://bharatkosh.gov.in/BKePay',
    'ldoOldNocListByColony' => 'https://bharatkosh.gov.in/BKePay',
    'oldAllDemandByPropertyId' => 'https://bharatkosh.gov.in/BKePay',

    //  'oldDemandByPropertyId' => 'https://ldo.gov.in/eDhartiAPI/Api/GetLatestDemand/ByPropertyID',
    // 'propertyDocList' => 'https://ldo.gov.in/eDhartiAPI/Api/GetValues/PropertyDocList?PropertyID=',
    // 'ldoOldNocListByColony' => 'https://ldo.gov.in/edhartiapi/Api/NOCList/ListbyColony',
    // 'oldAllDemandByPropertyId' => 'https://ldo.gov.in/eDhartiAPI/Api/GetAllDemand/ByPropertyID',
    'MAX_INACTIVE_DAYS_FOR_REGISTERED_USER' => 30, // Maximum inactive days before deactivating a registered user added by Swati 20-03-2025
    'MAX_INACTIVE_DAYS_AFTER_APPLICATION_DISPOSED' => 30, // Maximum inactive days after an application is disposed added by Swati 20-03-2025


];
