<?php

return [

    'show_custom_fields' => true,
    'custom_fields' => [
        'phone' => [
            'type' => 'text',
            'label' => 'Mobile phone',
            'placeholder' => '',
            'rules' => 'nullable|string|max:14',
            'required' => false,
        ],
        'fb_link' => [
            'type' => 'text',
            'label' => 'facebook',
            'placeholder' => '',
            'rules' => 'nullable|string|max:255',
            'required' => false,
        ]
    ]
];
