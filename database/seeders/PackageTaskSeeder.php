<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('task_category')->insert([
        //     ['name' => '1 Year to 6 Months before'],
        //     ['name' => '9 Months to 6 Months before'],
        //     ['name' => '3 Months to 1 Month before'],
        //     ['name' => '6 Months to 3 Months before'],
        //     ['name' => '3 Months before'],
        //     ['name' => '3 Months to 1 Month before'],
        //     ['name' => '1 Month before'],
        //     ['name' => '1 Week before and Wedding Day'],
        //     ['name' => 'Wedding Day'],
        //     ['name' => '6 Months after Wedding Day'],

        // ]);
        
        // Define package and department IDs
        $categories = [
            '1 Year to 6 Months before' => 1,
            '9 Months to 6 Months before' => 2,
            '3 Months to 1 Month before' => 3,
            '6 Months to 3 Months before' => 4,
            '3 Months before' => 5,
            '3 Months to 1 Month before' => 6,
            '1 Month before' => 7,
            '1 Week before and Wedding Day' => 8,
            'Wedding Day' => 9,
            '6 Months after Wedding Day' => 10
        ];

        $packages = [
            'Ruby' => 1,
            'Garnet' => 2,
            'Emerald' => 3,
            'Infinity' => 4,
            'Sapphire' => 5
        ];

        $departments = [
            'Catering' => 1,
            'HairAndMakeup' => 2,
            'PhotoAndVideo' => 3,
            'Designing' => 4,
            'Entertainment' => 5,
            'Coordination' => 6
        ];

        $tasks = [
            'Ruby' => [
                'Coordination' => [
                    '1 Year to 6 Months before' => [
                        'Initial meeting with the couple',
                        'Confirm team availability',
                        'Distribution of tasks and assigning teams',
                        'Bring down memo for each team leaders of each department',
                        'Monitor each department',
                    ],
                    '6 Months to 3 Months before' => [
                        'Finalize event flow (Consult with Clients)',
                    ],
                    '3 Months to 1 Month before' => [
                        'Oversee final preparations and ensure all departments have necessary resources'
                    ],
                    '1 Week before and Wedding Day' => [
                        'Final check-ins with departments',
                        'Day-of oversight, monitoring each departments progress',
                    ],
                ],
                'Catering' => [
                    '9 Months to 6 Months before' => [
                        'Meeting for food planning',
                        'Plan and design the wedding menu',
                    ],
                    '6 Months to 3 Months before' => [
                        'Conduct food tasting with the couple',
                        'Coordinate logistics with the venue for catering',
                        'Plan chair and table setup to match the wedding theme color',
                    ],
                    '1 Month before' => [
                        'Finalize setup plan with the designing team for the reception',
                    ],
                    'Wedding Day' => [
                        'Buffet Setup',
                        'Modern Design of wedding reception set up',
                        'Clean-up after service completion',
                    ]
                ],
                'HairAndMakeup' => [
                    '3 Months before' => [
                        'Initial consultations for makeup styles',
                        'Meeting with the bride and groom to discuss the entourage theme',
                        'Discuss hairstyle and makeup styles with the couple'
                    ],
                    'Wedding Day' => [
                        'Traditional Makeup for the bride',
                        'Early setup for bride, groom, and entourage makeup',
                        'Makeup for pre-ceremony photo and video shoots',
                        'Post-ceremony touch-ups as needed',
                    ]
                ],
                'Designing' => [
                    '9 Months to 6 Months before' => [
                        'Plan the weddings overall theme and identify props to match the motif',
                    ],
                    '3 Months to 1 Month before' => [
                        'Finalize aisle setup, walkway, altar, and reception décor',
                        'Coordinate with the catering team for the design of the reception space',
                        'Entourage flowers choices Bouquet/Corsage'
                    ],
                    'Wedding Day' => [
                        'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor',
                        'Final touch-ups on decorative elements before guests arrive',
                    ],
                ],
                'Entertainment' => [
                    '6 Months to 3 Months before' => [
                        'Meeting to discuss the tone of the reception (formal or playful)',
                        'Plan reception activities based on the couple preferences'
                    ],
                    '1 Month before' => [
                        'Check sound and lights equipment',
                        'Discuss music preference for both wedding ceremony and reception'
                    ],
                    'Wedding Day' => [
                        'Setup lights, sound, and professional DJ',
                        'Coordinate with the emcee for announcements and flow of events',
                        'Set up reception activities like games and first dance',
                        'Announce meals, orchestrate the couples first dance, manage speeches, and ensure music timing',
                    ]
                ],
            ],
            'Garnet' => [
                'Coordination' => [
                    '1 Year to 6 Months before' => [
                        'Initial meeting with the couple',
                        'Confirm team availability',
                        'Distribution of tasks and assigning teams',
                        'Bring down memo for each team leaders of each department',
                        'Monitor each department',
                    ],
                    '6 Months to 3 Months before' => [
                        'Finalize event flow (Consult with Clients)',
                    ],
                    '3 Months to 1 Month before' => [
                        'Oversee final preparations and ensure all departments have necessary resources'
                    ],
                    '1 Week before and Wedding Day' => [
                        'Final check-ins with departments',
                        'Day-of oversight, monitoring each departments progress',
                    ],
                ],
                'Catering' => [
                    '9 Months to 6 Months before' => [
                        'Meeting for food planning',
                        'Plan and design the wedding menu',
                    ],
                    '6 Months to 3 Months before' => [
                        'Conduct food tasting with the couple',
                        'Coordinate logistics with the venue for cater',
                        'Plan chair and table setup to match the weddings theme color',
                    ],
                    '1 Month before' => [
                        'Finalize setup plan with the designing team for the reception',
                    ],
                    'Wedding Day' => [
                        'Buffet Setup',
                        'Modern Design of wedding reception set up',
                        'Oversee food service, including coordination with the entertainment team for timing',
                        'Clean-up after service completion',
                    ]
                ],
                'HairAndMakeup' => [
                    '3 Months before' => [
                        'Initial consultations for makeup styles',
                        'Meeting with the bride and groom to discuss the entourage theme',
                    ],
                    'Wedding Day' => [
                        'Airbrush makeup for the bride',
                        'Early setup for bride, groom, and entourage makeup',
                        'Makeup for pre-ceremony photo and video shoots',
                    ],
                ],
                'PhotoAndVideo' => [
                    '3 Months before' => [
                        'Meeting with the couple and coordinator to discuss themes and concepts for video and album'
                    ],
                    '1 Month before' => [
                        'Confirm shot lists and key moments with the couple and coordinator'
                    ],
                    'Wedding Day' => [
                        'Setup Aerial Pilot/Drone',
                        'Setup Projector Screen',
                        'Edit the video for a same-day montage',
                    ],
                    '6 Months after Wedding Day' => [
                        'File transfer of photos and video to the couple'
                    ],
                ],
                'Designing' => [
                    '9 Months to 6 Months before' => [
                        'Plan the weddings overall theme and identify props to match the motif',
                    ],
                    '3 Months to 1 Month before' => [
                        'Finalize aisle setup, walkway, altar, and reception décor',
                        'Coordinate with the catering team for the design of the reception space',
                        'Entourage flowers choices Bouquet/Corsage',
                        'Designing of Garden Floristry'
                    ],
                    'Wedding Day' => [
                        'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor',
                        'Final touch-ups on decorative elements before guests arrive',
                    ],
                ],
                'Entertainment' => [
                    '6 Months to 3 Months before' => [
                        'Meeting to discuss the tone of the reception (formal or playful)',
                        'Plan reception activities based on the couple preferences'
                    ],
                    '1 Month before' => [
                        'Check sound and lights equipment',
                        'Discuss music preference for both wedding ceremony and reception'
                    ],
                    'Wedding Day' => [
                        'Setup lights, sound, and professional DJ',
                        'Coordinate with the emcee for announcements and flow of events',
                        'Set up reception activities like games and first dance',
                    ]
                ],
            ],
            'Emerald' => [
                'Coordination' => [
                    '1 Year to 6 Months before' => [
                        'Initial meeting with the couple',
                        'Confirm team availability',
                        'Distribution of tasks and assigning teams',
                        'Bring down memo for each team leaders of each department',
                        'Monitor each department',
                    ],
                    '6 Months to 3 Months before' => [
                        'Finalize event flow (Consult with Clients)',
                    ],
                    '3 Months to 1 Month before' => [
                        'Oversee final preparations and ensure all departments have necessary resources'
                    ],
                    '1 Week before and Wedding Day' => [
                        'Final check-ins with departments',
                        'Day-of oversight, monitoring each departments progress',
                    ],
                ],
                'Catering' => [
                    '9 Months to 6 Months before' => [
                        'Meeting for food planning',
                        'Plan and design the weddings menu',
                    ],
                    '6 Months to 3 Months before' => [
                        'Conduct food tasting with the couple',
                        'Coordinate logistics with the venue for cater',
                        'Plan chair and table setup to match the weddings theme color',
                    ],
                    '1 Month before' => [
                        'Finalize setup plan with the designing team for the reception',
                    ],
                    'Wedding Day' => [
                        'Buffet Setup',
                        'Modern Design of weddings reception set up',
                        'Clean-up after service completion',
                    ]
                ],
                'HairAndMakeup' => [
                    '3 Months before' => [
                        'Initial consultations for makeup styles',
                        'Meeting with the bride and groom to discuss the entourage theme',
                        'Prenup Traditional hair and makeup'
                    ],
                    'Wedding Day' => [
                        'Airbrush makeup for the bride',
                        'Early setup for bride, groom, and entourage makeup',
                        'Makeup for pre-ceremony photo and video shoots',
                    ],
                ],
                'PhotoAndVideo' => [
                    '3 Months before' => [
                        'Meeting with the couple and coordinator to discuss themes and concepts for video and album',
                        'Prenuptial Pictorial Session'
                    ],
                    '1 Month before' => [
                        'Confirm shot lists and key moments with the couple and coordinator',
                        'Designing of Wedding Photo Frame'
                    ],
                    'Wedding Day' => [
                        'Setup Photobooth',
                        'Setup Aerial Pilot/Drone',
                        'Setup Projector Screen',
                        'Edit the video for a same-day montage',
                    ],
                    '6 Months after Wedding Day' => [
                        'File transfer of photos and video to the couple'
                    ],
                ],
                'Designing' => [
                    '9 Months to 6 Months before' => [
                        'Plan the weddings overall theme and identify props to match the motif',
                        'Design and coordinate the invitation cards'
                    ],
                    '3 Months to 1 Month before' => [
                        'Finalize aisle setup, walkway, altar, and reception decor',
                        'Coordinate with the cater team for the design of the reception space',
                        'Entourage flowers choices Bouquet/Corsage',
                        'Designing of Garden Floristry'
                    ],
                    'Wedding Day' => [
                        'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor',
                        'Final touch-ups on decorative elements before guests arrive',
                    ],
                ],
                'Entertainment' => [
                    '6 Months to 3 Months before' => [
                        'Meeting to discuss the tone of the reception (formal or playful)',
                        'Plan reception activities based on the couple preferences'
                    ],
                    '1 Month before' => [
                        'Check sound and lights equipment',
                        'Discuss music preference for both weddings ceremony and reception'
                    ],
                    'Wedding Day' => [
                        'Setup lights, sound, and professional DJ',
                        'Coordinate with the emcee for announcements and flow of events',
                        'Set up reception activities like games and first dance',
                    ]
                ],
            ],
            'Infinity' => [
                'Coordination' => [
                    '1 Year to 6 Months before' => [
                        'Initial meeting with the couple',
                        'Confirm team availability',
                        'Distribution of tasks and assigning teams',
                        'Bring down memo for each team leaders of each department',
                        'Monitor each department',
                    ],
                    '6 Months to 3 Months before' => [
                        'Finalize event flow (Consult with Clients)',
                    ],
                    '3 Months to 1 Month before' => [
                        'Oversee final preparations and ensure all departments have necessary resources'
                    ],
                    '1 Week before and Wedding Day' => [
                        'Final check-ins with departments',
                        'Day-of oversight, monitoring each departments progress',
                    ],
                ],
                'Catering' => [
                    '9 Months to 6 Months before' => [
                        'Meeting for food planning',
                        'Plan and design the weddings menu',
                    ],
                    '6 Months to 3 Months before' => [
                        'Conduct food tasting with the couple',
                        'Coordinate logistics with the venue for cater',
                        'Plan chair and table setup to match the weddings theme color',
                    ],
                    '1 Month before' => [
                        'Finalize setup plan with the designing team for the reception',
                    ],
                    'Wedding Day' => [
                        'Buffet Setup',
                        'Modern Design of weddings reception set up',
                        'Clean-up after service completion',
                    ]
                ],
                'HairAndMakeup' => [
                    '3 Months before' => [
                        'Initial consultations for makeup styles',
                        'Meeting with the bride and groom to discuss the entourage theme',
                        'Prenup Traditional hair and makeup'
                    ],
                    'Wedding Day' => [
                        'Airbrush makeup for the bride',
                        'Early setup for bride, groom, and entourage makeup',
                        'Entourage makeup',
                        'Makeup for pre-ceremony photo and video shoots',
                    ],
                ],
                'PhotoAndVideo' => [
                    '3 Months before' => [
                        'Meeting with the couple and coordinator to discuss themes and concepts for video and album',
                        'Prenuptial Pictorial Session'
                    ],
                    '1 Month before' => [
                        'Confirm shot lists and key moments with the couple and coordinator',
                        'Designing of Wedding Photo Frame'
                    ],
                    'Wedding Day' => [
                        'Setup Photobooth',
                        'Setup Aerial Pilot/Drone',
                        'Setup Projector Screen',
                        'Same day edit video prenuptial photo and engagement session',
                        'Onside Photo same day edit with photoalbum with wedding highlights',
                        'Transfer raw photos for clients on the same day'
                    ],
                    '6 Months after Wedding Day' => [
                        'File transfer of photos and video to the couple'
                    ]
                ],
                'Designing' => [
                    '9 Months to 6 Months before' => [
                        'Plan the weddings overall theme and identify props to match the motif',
                        'Design and coordinate the invitation cards'
                    ],
                    '3 Months to 1 Month before' => [
                        'Finalize aisle setup, walkway, altar, and reception decor',
                        'Coordinate with the cater team for the design of the reception space',
                        'Entourage flowers choices Bouquet/Corsage',
                        'Designing of Garden Floristry'
                    ],
                    'Wedding Day' => [
                        'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor',
                        'Final touch-ups on decorative elements before guests arrive',
                    ]
                ],
                'Entertainment' => [
                    '6 Months to 3 Months before' => [
                        'Meeting to discuss the tone of the reception (formal or playful)',
                        'Plan reception activities based on the couple preferences'
                    ],
                    '1 Month before' => [
                        'Check sound and lights equipment',
                        'Discuss music preference for both weddings ceremony and reception'
                    ],
                    'Wedding Day' => [
                        'Setup lights, sound, and channel mixer music operator',
                        'Coordinate with the emcee for announcements and flow of events',
                        'Set up reception activities like games and first dance',
                    ]
                ],
            ],
            'Sapphire' => [
                'Coordination' => [
                    '1 Year to 6 Months before' => [
                        'Initial meeting with the couple',
                        'Confirm team availability',
                        'Distribution of tasks and assigning teams',
                        'Bring down memo for each team leaders of each department',
                        'Monitor each department',
                    ],
                    '6 Months to 3 Months before' => [
                        'Finalize event flow (Consult with Clients)',
                    ],
                    '3 Months to 1 Month before' => [
                        'Oversee final preparations and ensure all departments have necessary resources'
                    ],
                    '1 Week before and Wedding Day' => [
                        'Final check-ins with departments',
                        'Day-of oversight, monitoring each departments progress',
                    ],
                ],
                'Catering' => [
                    '9 Months to 6 Months before' => [
                        'Meeting for food planning',
                        'Plan and design the weddings menu',
                    ],
                    '6 Months to 3 Months before' => [
                        'Conduct food tasting with the couple',
                        'Coordinate logistics with the venue for cater',
                        'Plan chair and table setup to match the weddings theme color',
                    ],
                    '1 Month before' => [
                        'Finalize setup plan with the designing team for the reception',
                    ],
                    'Wedding Day' => [
                        'Buffet Setup',
                        'Setup food cart choice',
                        'Modern Design of weddings reception set up',
                        'Clean-up after service completion',
                    ]
                ],
                'HairAndMakeup' => [
                    '3 Months before' => [
                        'Initial consultations for makeup styles',
                        'Meeting with the bride and groom to discuss the entourage theme',
                        'Prenup Traditional hair and makeup'
                    ],
                    'Wedding Day' => [
                        'Airbrush makeup for the bride',
                        'Early setup for bride, groom, and entourage makeup',
                        'Entourage makeup',
                        'Makeup for pre-ceremony photo and video shoots',
                    ],
                ],
                'PhotoAndVideo' => [
                    '3 Months before' => [
                        'Meeting with the couple and coordinator to discuss themes and concepts for video and album',
                        'Prenuptial Pictorial Session'
                    ],
                    '1 Month before' => [
                        'Confirm shot lists and key moments with the couple and coordinator',
                        'Designing of weddings Photo Frame'
                    ],
                    'Wedding Day' => [
                        'Setup Photobooth',
                        'Setup Aerial Pilot/Drone',
                        'Setup Projector Screen',
                        'Same day edit video prenuptial photo and engagement session',
                        'Onside Photo same day edit with photoalbum with weddings highlights',
                        'Transfer raw photos for clients on the same day'
                    ],
                    '6 Months after Wedding Day' => [
                        'File transfer of photos and video to the couple'
                        ]
                ],
                'Designing' => [
                    '9 Months to 6 Months before' => [
                        'Plan the weddings overall theme and identify props to match the motif',
                        'Design and coordinate the invitation cards'
                    ],  
                    '3 Months to 1 Month before' => [
                        'Finalize aisle setup, walkway, altar, and reception décor',
                        'Coordinate with the catering team for the design of the reception space',
                        'Entourage flowers choices Bouquet, Boutonniere, and Corsage',
                        'Designing of Garden Floristry',
                        'Setup LED Wall'
                    ],
                    'Wedding Day' => [
                        'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor',
                        'Final touch-ups on decorative elements before guests arrive'
                    ]
                    
                
                ],
                'Entertainment' => [
                    '6 Months to 3 Months before' => [
                        'Meeting to discuss the tone of the reception (formal or playful)',
                        'Plan reception activities based on the couple preferences'
                    ],
                    '1 Month before' => [
                        'Check sound and lights equipment',
                        'Discuss music preference for both weddings ceremony and reception'
                    ],
                    'Wedding Day' => [
                        'Setup lights, sound, and channel mixer music operator',
                        'Deploy Acoustic Band',
                        'Coordinate with the emcee for announcements and flow of events',
                        'Set up reception activities like games and first dance',
                    ]
                ],
            ],
        ];

        foreach ($tasks as $packageName => $departmentsTasks) {
            $packageId = $packages[$packageName];
            foreach ($departmentsTasks as $departmentName => $categoriesTasks) {
                $departmentId = $departments[$departmentName];
                foreach ($categoriesTasks as $categoryName => $tasksArray) {
                    $taskCategoryId = $categories[$categoryName];
                    foreach ($tasksArray as $taskName) {
                        DB::table('package_task_department')->insert([
                            'package_id' => $packageId,
                            'department_id' => $departmentId,
                            'task_category_id' => $taskCategoryId,
                            'name' => $taskName,
                        ]);
                    }
                }
            }
        }
    }
}
