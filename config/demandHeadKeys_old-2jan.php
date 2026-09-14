<?php
return [
    'DEM_AF_P' => [
        /* [
            'key' => 'allotment_fee_date_from',
            'label' => 'Date From',
            'type' => 'date',
            'required' => true
        ],
        [
            'key' => 'allotment_fee_date_to',
            'label' => 'Date To',
            'type' => 'date',
            'required' => true
        ] */
        [
            'key' => 'allotment_fee_land_area',
            'label' => "Area of property",
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'allocation_type_land_rate',
            'label' => "Land Rate",
            'type' => 'number',
            'required' => true
        ]
    ],
    'DEM_LF_GR' => [
        [
            'key' => 'ground_rent_land_area',
            'label' => "Area of property",
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'ground_rent_land_rate',
            'label' => "Land Rate",
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'ground_rent_property_type',
            'label' => "Property Type",
            'type' => 'number',
            'required' => true
        ],
    ],
    'DEM_UEI' => [
        [
            'key' => 'is_transfer_done',
            'label' => 'Is Transfer Done',
            'type' => 'radio',
            'required' => true
        ],
        [
            'key' => 'unearned_increase_land_value',
            'label' => 'Land Value',
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'unearned_increase_consideration_value',
            'label' => 'Consideration Value',
            'type' => 'number',
            'requiredIf' => 'is_transfer_done=1'
        ],
        [
            'key' => 'unearned_increase_transfer_date',
            'label' => 'Transfer Date',
            'type' => 'date',
            'requiredIf' => 'is_transfer_done=1'
        ],
    ],
    'DEM_CONV_CHG' => [
        /*  [
            'key' => 'conversion_land_value',
            'label' => 'Land Value',
            'type' => 'number',
            'required' => true
        ], */
        [
            'key' => 'conversion_land_rate',
            'label' => 'Land Rate',
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'conversion_plot_area',
            'label' => 'Plot Area',
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'conversion_remission',
            'label' => 'Remission Allowed',
            'type' => 'checkbox'
        ],
        [
            'key' => 'conversion_charges',
            'label' => 'Conversion Charges',
            'type' => 'number'
        ],
        [
            'key' => 'conversion_remission_amount',
            'label' => 'Allowed Remission Amount',
            'type' => 'number'
        ],
          [
            'key' => 'conversion_surcharge_amount',
            'label' => 'Applied Surcharge Amount',
            'type' => 'number'
        ],
        [
            'key' => 'conversion_formula',
            'label' => 'Conversion formula',
            'type' => 'text'
        ],
    ],
    'DEM_LUC_RC' => [
        [
            'key' => 'partial_change',
            'label' => 'Mixed Use',
            'type' => 'checkbox'
        ],
        [
            'key' => 'luc_land_value', //commercial land value of property
            'label' => 'Land Value',
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'luc_TBUA',
            'label' => 'Total Built up Area',
            'type' => 'number',
            'requiredIf' => 'partial_change'
        ],
        [
            'key' => 'luc_BUAC',
            'label' => 'Area to be used as commercial',
            'type' => 'number',
            'requiredIf' => 'partial_change'
        ]
    ],
    'DEM_SLET_CHG' => [
        [
            'key' => 'penal_subletting',
            'label' => 'Penalty Added',
            'type' => 'checkbox',
        ],
        [
            'key' => 'annual_subletting_income',
            'label' => 'Annual Income',
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'subletting_start_date',
            'label' => 'Start Date',
            'type' => 'date',
            'requiredIf' => 'penal_subletting'
        ],
        [
            'key' => 'subletting_confirmation_date',
            'label' => 'Subletting Confirmation Date',
            'type' => 'date',
            'requiredIf' => 'penal_subletting'
        ],
    ],
    'DEM_PENAL_STANDARD' => [
        [
            'key' => 'standard_penalty_land_value',
            'label' => 'Land alue',
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'standard_penalty_description',
            'label' => 'Description',
            'type' => 'text',
            'required' => true
        ]
    ],
    'DEM_OTHER' => [
        [
            'key' => 'others_description',
            'label' => 'Description',
            'type' => 'text',
            'required' => true
        ]
    ],
    'no_demand_head' => [
        [
            'key' => 'new_allotment_radio',
            'type' => 'radio',
            'required' => true
        ],
        [
            'key' => 'allocation_type_radio',
            'type' => 'radio',
            'required' => 'new_allotment_radio=1'
        ],
        [
            'key' => 'allocation_start_date',
            'type' => 'date',
            'required' => 'allocation_type_radio'
        ],
        [
            'key' => 'allocation_end_date',
            'type' => 'date',
            'required' => 'allocation_type_radio=0'
        ]
    ],
    'DEM_MANUAL' => [
        [
            'key' => 'manual_title',
            'label' => 'Title',
            'type' => 'text',
            'required' => true
        ],
        [
            'key' => 'manual_amount',
            'label' => 'Amount',
            'type' => 'number',
            'required' => true
        ],
        [
            'key' => 'manual_date_from',
            'label' => 'Date From',
            'type' => 'date',
        ],
        [
            'key' => 'manual_date_to',
            'label' => 'Date To',
            'type' => 'date',
        ],
        [
            'key' => 'manual_description',
            'label' => 'Description',
            'type' => 'text',
            'required' => true
        ],

    ]
];
