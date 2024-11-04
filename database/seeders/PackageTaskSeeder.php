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

        $departments = [
            'Catering' => 1,
            'HairAndMakeup' => 2,
            'PhotoAndVideo' => 3,
            'Designing' => 4,
            'Entertainment' => 5,
            'Coordination' => 6
        ];

        $tasks = [
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
                    'Discuss additional food cart (Mobile Bar or Donut Wall)'
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
        ];

        foreach ($tasks as $departmentName => $categoriesTasks) {
            $departmentId = $departments[$departmentName];
            foreach ($categoriesTasks as $categoryName => $tasksArray) {
                $taskCategoryId = $categories[$categoryName];
                foreach ($tasksArray as $taskName) {
                    DB::table('tasks')->insert([
                        'department_id' => $departmentId,
                        'task_category_id' => $taskCategoryId,
                        'name' => $taskName,
                    ]);
                }
            }
        }



        DB::table('task_package')->insert([
            ['package_id' => 5 , 'task_id' => 1],
            ['package_id' => 5 , 'task_id' => 2],
            ['package_id' => 5 , 'task_id' => 3],
            ['package_id' => 5 , 'task_id' => 4],
            ['package_id' => 5 , 'task_id' => 5],
            ['package_id' => 5 , 'task_id' => 6],
            ['package_id' => 5 , 'task_id' => 7],
            ['package_id' => 5 , 'task_id' => 8],
            ['package_id' => 5 , 'task_id' => 9],
            ['package_id' => 5 , 'task_id' => 10],
            ['package_id' => 5 , 'task_id' => 11],
            ['package_id' => 5 , 'task_id' => 12],
            ['package_id' => 5 , 'task_id' => 13],
            ['package_id' => 5 , 'task_id' => 14],
            ['package_id' => 5 , 'task_id' => 15],
            ['package_id' => 5 , 'task_id' => 16],
            ['package_id' => 5 , 'task_id' => 17],
            ['package_id' => 5 , 'task_id' => 18],
            ['package_id' => 5 , 'task_id' => 19],
            ['package_id' => 5 , 'task_id' => 20],
            ['package_id' => 5 , 'task_id' => 21],
            ['package_id' => 5 , 'task_id' => 22],
            ['package_id' => 5 , 'task_id' => 23],
            ['package_id' => 5 , 'task_id' => 24],
            ['package_id' => 5 , 'task_id' => 25],
            ['package_id' => 5 , 'task_id' => 26],
            ['package_id' => 5 , 'task_id' => 27],
            ['package_id' => 5 , 'task_id' => 28],
            ['package_id' => 5 , 'task_id' => 29],
            ['package_id' => 5 , 'task_id' => 30],
            ['package_id' => 5 , 'task_id' => 31],
            ['package_id' => 5 , 'task_id' => 32],
            ['package_id' => 5 , 'task_id' => 33],
            ['package_id' => 5 , 'task_id' => 34],
            ['package_id' => 5 , 'task_id' => 35],
            ['package_id' => 5 , 'task_id' => 36],
            ['package_id' => 5 , 'task_id' => 37],
            ['package_id' => 5 , 'task_id' => 38],
            ['package_id' => 5 , 'task_id' => 39],
            ['package_id' => 5 , 'task_id' => 40],
            ['package_id' => 5 , 'task_id' => 41],
            ['package_id' => 5 , 'task_id' => 42],
            ['package_id' => 5 , 'task_id' => 43],
            ['package_id' => 5 , 'task_id' => 44],
            ['package_id' => 5 , 'task_id' => 45],
            ['package_id' => 5 , 'task_id' => 46],
            ['package_id' => 5 , 'task_id' => 47],
            ['package_id' => 5 , 'task_id' => 48],
            ['package_id' => 5 , 'task_id' => 49],
            ['package_id' => 5 , 'task_id' => 50],
            ['package_id' => 5 , 'task_id' => 51],
            ['package_id' => 5 , 'task_id' => 52],
            ['package_id' => 5 , 'task_id' => 53],
            ['package_id' => 5 , 'task_id' => 54],
            ['package_id' => 5 , 'task_id' => 55],
        ]);

        DB::table('task_package')->insert([
            ['package_id' => 4 , 'task_id' => 2],
            ['package_id' => 4 , 'task_id' => 1],
            ['package_id' => 4 , 'task_id' => 3],
            ['package_id' => 4 , 'task_id' => 4],
            ['package_id' => 4 , 'task_id' => 5],
            ['package_id' => 4 , 'task_id' => 6],
            ['package_id' => 4 , 'task_id' => 7],
            ['package_id' => 4 , 'task_id' => 8],
            ['package_id' => 4 , 'task_id' => 9],
            ['package_id' => 4 , 'task_id' => 10],
            ['package_id' => 4 , 'task_id' => 11],
            ['package_id' => 4 , 'task_id' => 12],
            ['package_id' => 4 , 'task_id' => 13],
            ['package_id' => 4 , 'task_id' => 14],
            ['package_id' => 4 , 'task_id' => 15],
            ['package_id' => 4 , 'task_id' => 17],
            ['package_id' => 4 , 'task_id' => 18],
            ['package_id' => 4 , 'task_id' => 20],
            ['package_id' => 4 , 'task_id' => 21],
            ['package_id' => 4 , 'task_id' => 22],
            ['package_id' => 4 , 'task_id' => 23],
            ['package_id' => 4 , 'task_id' => 24],
            ['package_id' => 4 , 'task_id' => 25],
            ['package_id' => 4 , 'task_id' => 26],
            ['package_id' => 4 , 'task_id' => 27],
            ['package_id' => 4 , 'task_id' => 28],
            ['package_id' => 4 , 'task_id' => 29],
            ['package_id' => 4 , 'task_id' => 30],
            ['package_id' => 4 , 'task_id' => 31],
            ['package_id' => 4 , 'task_id' => 32],
            ['package_id' => 4 , 'task_id' => 33],
            ['package_id' => 4 , 'task_id' => 34],
            ['package_id' => 4 , 'task_id' => 35],
            ['package_id' => 4 , 'task_id' => 36],
            ['package_id' => 4 , 'task_id' => 37],
            ['package_id' => 4 , 'task_id' => 38],
            ['package_id' => 4 , 'task_id' => 39],
            ['package_id' => 4 , 'task_id' => 40],
            ['package_id' => 4 , 'task_id' => 41],
            ['package_id' => 4 , 'task_id' => 42],
            ['package_id' => 4 , 'task_id' => 43],
            ['package_id' => 4 , 'task_id' => 44],
            ['package_id' => 4 , 'task_id' => 45],
            ['package_id' => 4 , 'task_id' => 46],
            ['package_id' => 4 , 'task_id' => 47],
            ['package_id' => 4 , 'task_id' => 48],
            ['package_id' => 4 , 'task_id' => 49],
            ['package_id' => 4 , 'task_id' => 50],
            ['package_id' => 4 , 'task_id' => 51],
            ['package_id' => 4 , 'task_id' => 52],
            ['package_id' => 4 , 'task_id' => 53],
            ['package_id' => 4 , 'task_id' => 54],
            ['package_id' => 4 , 'task_id' => 55],
        ]);

        DB::table('task_package')->insert([
            ['package_id' => 3 , 'task_id' => 2],
            ['package_id' => 3 , 'task_id' => 1],
            ['package_id' => 3 , 'task_id' => 3],
            ['package_id' => 3 , 'task_id' => 4],
            ['package_id' => 3 , 'task_id' => 5],
            ['package_id' => 3 , 'task_id' => 6],
            ['package_id' => 3 , 'task_id' => 7],
            ['package_id' => 3 , 'task_id' => 8],
            ['package_id' => 3 , 'task_id' => 9],
            ['package_id' => 3 , 'task_id' => 10],
            ['package_id' => 3 , 'task_id' => 11],
            ['package_id' => 3 , 'task_id' => 12],
            ['package_id' => 3 , 'task_id' => 13],
            ['package_id' => 3 , 'task_id' => 14],
            ['package_id' => 3 , 'task_id' => 15],
            ['package_id' => 3 , 'task_id' => 17],
            ['package_id' => 3 , 'task_id' => 18],
            ['package_id' => 3 , 'task_id' => 20],
            ['package_id' => 3 , 'task_id' => 21],
            ['package_id' => 3 , 'task_id' => 22],
            ['package_id' => 3 , 'task_id' => 23],
            ['package_id' => 3 , 'task_id' => 24],
            ['package_id' => 3 , 'task_id' => 26],
            ['package_id' => 3 , 'task_id' => 27],
            ['package_id' => 3 , 'task_id' => 28],
            ['package_id' => 3 , 'task_id' => 29],
            ['package_id' => 3 , 'task_id' => 30],
            ['package_id' => 3 , 'task_id' => 31],
            ['package_id' => 3 , 'task_id' => 32],
            ['package_id' => 3 , 'task_id' => 33],
            ['package_id' => 3 , 'task_id' => 34],
            ['package_id' => 3 , 'task_id' => 35],
            ['package_id' => 3 , 'task_id' => 38],
            ['package_id' => 3 , 'task_id' => 39],
            ['package_id' => 3 , 'task_id' => 40],
            ['package_id' => 3 , 'task_id' => 41],
            ['package_id' => 3 , 'task_id' => 42],
            ['package_id' => 3 , 'task_id' => 43],
            ['package_id' => 3 , 'task_id' => 44],
            ['package_id' => 3 , 'task_id' => 45],
            ['package_id' => 3 , 'task_id' => 46],
            ['package_id' => 3 , 'task_id' => 47],
            ['package_id' => 3 , 'task_id' => 48],
            ['package_id' => 3 , 'task_id' => 49],
            ['package_id' => 3 , 'task_id' => 50],
            ['package_id' => 3 , 'task_id' => 51],
            ['package_id' => 3 , 'task_id' => 52],
            ['package_id' => 3 , 'task_id' => 53],
            ['package_id' => 3 , 'task_id' => 54],
            ['package_id' => 3 , 'task_id' => 55],
        ]);

        DB::table('task_package')->insert([
            ['package_id' => 2 , 'task_id' => 2],
            ['package_id' => 2 , 'task_id' => 1],
            ['package_id' => 2 , 'task_id' => 3],
            ['package_id' => 2 , 'task_id' => 4],
            ['package_id' => 2 , 'task_id' => 5],
            ['package_id' => 2 , 'task_id' => 6],
            ['package_id' => 2 , 'task_id' => 7],
            ['package_id' => 2 , 'task_id' => 8],
            ['package_id' => 2 , 'task_id' => 9],
            ['package_id' => 2 , 'task_id' => 10],
            ['package_id' => 2 , 'task_id' => 11],
            ['package_id' => 2 , 'task_id' => 12],
            ['package_id' => 2 , 'task_id' => 13],
            ['package_id' => 2 , 'task_id' => 14],
            ['package_id' => 2 , 'task_id' => 15],
            // ['package_id' 2> 2 , 'task_id' => 16],
            ['package_id' => 2 , 'task_id' => 17],
            ['package_id' => 2 , 'task_id' => 18],
            // ['package_id' 2> 2 , 'task_id' => 19],
            ['package_id' => 2 , 'task_id' => 20],
            ['package_id' => 2 , 'task_id' => 21],
            ['package_id' => 2 , 'task_id' => 22],
            // ['package_id' => 2 , 'task_id' => 23],
            ['package_id' => 2 , 'task_id' => 24],
            // ['package_id' 2> 3 , 'task_id' => 25],
            ['package_id' => 2 , 'task_id' => 26],
            ['package_id' => 2 , 'task_id' => 27],
            ['package_id' => 2 , 'task_id' => 28],
            // ['package_id' => 2 , 'task_id' => 29],
            ['package_id' => 2 , 'task_id' => 30],
            // ['package_id' => 2 , 'task_id' => 31],
            // ['package_id' => 2 , 'task_id' => 32],
            ['package_id' => 2 , 'task_id' => 33],
            ['package_id' => 2 , 'task_id' => 34],
            ['package_id' => 2 , 'task_id' => 35],
            // ['package_id' 2> 3 , 'task_id' => 36],
            // ['package_id' 2> 3 , 'task_id' => 37],
            ['package_id' => 2 , 'task_id' => 38],
            ['package_id' => 2 , 'task_id' => 39],
            // ['package_id' => 2 , 'task_id' => 40],
            ['package_id' => 2 , 'task_id' => 41],
            ['package_id' => 2 , 'task_id' => 42],
            ['package_id' => 2 , 'task_id' => 43],
            ['package_id' => 2 , 'task_id' => 44],
            ['package_id' => 2 , 'task_id' => 45],
            ['package_id' => 2 , 'task_id' => 46],
            ['package_id' => 2 , 'task_id' => 47],
            ['package_id' => 2 , 'task_id' => 48],
            ['package_id' => 2 , 'task_id' => 49],
            ['package_id' => 2 , 'task_id' => 50],
            ['package_id' => 2 , 'task_id' => 51],
            ['package_id' => 2 , 'task_id' => 52],
            ['package_id' => 2 , 'task_id' => 53],
            ['package_id' => 2 , 'task_id' => 54],
            ['package_id' => 2 , 'task_id' => 55],
        ]);

        DB::table('task_package')->insert([
            ['package_id' => 1 , 'task_id' => 2],
            ['package_id' => 1 , 'task_id' => 1],
            ['package_id' => 1 , 'task_id' => 3],
            ['package_id' => 1 , 'task_id' => 4],
            ['package_id' => 1 , 'task_id' => 5],
            ['package_id' => 1 , 'task_id' => 6],
            ['package_id' => 1 , 'task_id' => 7],
            ['package_id' => 1 , 'task_id' => 8],
            ['package_id' => 1 , 'task_id' => 9],
            ['package_id' => 1 , 'task_id' => 10],
            ['package_id' => 1 , 'task_id' => 11],
            ['package_id' => 1 , 'task_id' => 12],
            ['package_id' => 1 , 'task_id' => 13],
            ['package_id' => 1 , 'task_id' => 14],
            ['package_id' => 1 , 'task_id' => 15],
            // ['package_id' 1> 2 , 'task_id' => 16],
            ['package_id' => 1 , 'task_id' => 17],
            ['package_id' => 1 , 'task_id' => 18],
            // ['package_id' 1> 2 , 'task_id' => 19],
            ['package_id' => 1 , 'task_id' => 20],
            ['package_id' => 1 , 'task_id' => 21],
            ['package_id' => 1 , 'task_id' => 22],
            // ['package_id' 1> 2 , 'task_id' => 23],
            ['package_id' => 1 , 'task_id' => 24],
            // ['package_id' 1> 3 , 'task_id' => 25],
            ['package_id' => 1 , 'task_id' => 26],
            ['package_id' => 1 , 'task_id' => 27],
            // ['package_id' => 1 , 'task_id' => 28],
            // // ['package_id' 1> 2 , 'task_id' => 29],
            // ['package_id' => 1 , 'task_id' => 30],
            // // ['package_id' 1> 2 , 'task_id' => 31],
            // // ['package_id' 1> 2 , 'task_id' => 32],
            // ['package_id' => 1 , 'task_id' => 33],
            // ['package_id' => 1 , 'task_id' => 34],
            // ['package_id' => 1 , 'task_id' => 35],
            // // ['package_id' 1> 3 , 'task_id' => 36],
            // // ['package_id' 1> 3 , 'task_id' => 37],
            // ['package_id' => 1 , 'task_id' => 38],
            ['package_id' => 1 , 'task_id' => 39],
            // ['package_id' 1> 2 , 'task_id' => 40],
            ['package_id' => 1 , 'task_id' => 41],
            ['package_id' => 1 , 'task_id' => 42],
            ['package_id' => 1 , 'task_id' => 43],
            ['package_id' => 1 , 'task_id' => 44],
            ['package_id' => 1 , 'task_id' => 45],
            ['package_id' => 1 , 'task_id' => 46],
            ['package_id' => 1 , 'task_id' => 47],
            ['package_id' => 1 , 'task_id' => 48],
            ['package_id' => 1 , 'task_id' => 49],
            ['package_id' => 1 , 'task_id' => 50],
            ['package_id' => 1 , 'task_id' => 51],
            ['package_id' => 1 , 'task_id' => 52],
            ['package_id' => 1 , 'task_id' => 53],
            ['package_id' => 1 , 'task_id' => 54],
            ['package_id' => 1 , 'task_id' => 55],
        ]);
    }

    
}
