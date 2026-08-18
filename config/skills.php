<?php

/**
 * Job categories, the Skill Badge catalog, and payment methods.
 *
 * Keeping these in one config file means the task-creation form, the
 * badge-award logic (App\Services\BadgeService), and the badge display
 * views all read from the same source of truth.
 */
return [

    // Job categories a task can be posted under. Keys are stored on
    // tasks.category; values are the human-readable label shown in the UI.
    'categories' => [
        'fishing'       => 'Fishing',
        'farming'       => 'Farming / Agriculture',
        'construction'  => 'Construction / Labor',
        'domestic_work' => 'Domestic Work',
        'transport'     => 'Transport / Delivery',
        'event_help'    => 'Event Help',
        'other'         => 'Other',
    ],

    // Skill badges a worker can earn. 'other' is deliberately excluded --
    // it's too generic a category to certify a specific skill.
    // 'threshold' is the number of *completed* jobs in that category
    // required before the badge is automatically awarded.
    'badges' => [
        'fishing' => [
            'label'     => 'Verified Fisher',
            'icon'      => '🎣',
            'threshold' => 3,
        ],
        'farming' => [
            'label'     => 'Verified Farm Hand',
            'icon'      => '🌾',
            'threshold' => 3,
        ],
        'construction' => [
            'label'     => 'Verified Builder',
            'icon'      => '🧱',
            'threshold' => 3,
        ],
        'domestic_work' => [
            'label'     => 'Verified Domestic Helper',
            'icon'      => '🧹',
            'threshold' => 3,
        ],
        'transport' => [
            'label'     => 'Verified Transporter',
            'icon'      => '🚚',
            'threshold' => 3,
        ],
        'event_help' => [
            'label'     => 'Verified Event Crew',
            'icon'      => '🎪',
            'threshold' => 3,
        ],
    ],

    // Accepted payment methods for the Payment Record & Receipt feature.
    'payment_methods' => [
        'cash'  => 'Cash',
        'bkash' => 'bKash',
        'nagad' => 'Nagad',
    ],
];
