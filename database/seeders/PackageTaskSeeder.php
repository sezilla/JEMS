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
            '6 Months to 3 Months before' => 3,
            '4 Months to 3 Months before' => 4,
            '3 Months to 1 Month before' => 5,
            '1 Month to 1 Week before' => 6,
            '1 Week before and Wedding Day' => 7,
            'Wedding Day' => 8,
            '6 Months after Wedding Day' => 9,
        ];

        $departments = [
            'Catering' => 1,
            'HairAndMakeup' => 2,
            'PhotoAndVideo' => 3,
            'Designing' => 4,
            'Entertainment' => 5,
            'Coordination' => 6
        ];

        // $tasks = [
        //     'Coordination' => [
        //         '1 Year to 6 Months before' => [
        //             ['name' => 'Initial meeting', 'description' => 'Initial meeting with the couple'],
        //             ['name' => 'Confirm availability', 'description' => 'Confirm team availability'],
        //             ['name' => 'Task distribution', 'description' => 'Distribution of tasks and assigning teams'],
        //             ['name' => 'Memo distribution', 'description' => 'Bring down memo for each team leaders of each department'],
        //             ['name' => 'Monitor departments', 'description' => 'Monitor each department'],
        //         ],
        //         '6 Months to 3 Months before' => [
        //             ['name' => 'Finalize event flow', 'description' => 'Finalize event flow (Consult with Clients)'],
        //         ],
        //         '3 Months to 1 Month before' => [
        //             ['name' => 'Oversee final prep', 'description' => 'Oversee final preparations and ensure all departments have necessary resources']
        //         ],
        //         '1 Week before and Wedding Day' => [
        //             ['name' => 'Final check-ins', 'description' => 'Final check-ins with departments'],
        //             ['name' => 'Day-of oversight', 'description' => 'Day-of oversight, monitoring each department’s progress'],
        //         ],
        //     ],
        //     'Catering' => [
        //         '9 Months to 6 Months before' => [
        //             ['name' => 'Food planning meeting', 'description' => 'Meeting for food planning'],
        //             ['name' => 'Menu planning', 'description' => 'Plan and design the wedding menu'],
        //         ],
        //         '6 Months to 3 Months before' => [
        //             ['name' => 'Food tasting', 'description' => 'Conduct food tasting with the couple'],
        //             ['name' => 'Coordinate venue logistics', 'description' => 'Coordinate logistics with the venue for catering'],
        //             ['name' => 'Table and chair setup plan', 'description' => 'Plan chair and table setup to match the wedding’s color theme'],
        //         ],
        //         '1 Month before' => [
        //             ['name' => 'Finalize setup plan', 'description' => 'Finalize setup plan with the designing team for the reception'],
        //             ['name' => 'Discuss food cart options', 'description' => 'Discuss additional food cart (Mobile Bar or Donut Wall)']
        //         ],
        //         'Wedding Day' => [
        //             ['name' => 'Buffet Setup', 'description' => 'Buffet Setup'],
        //             ['name' => 'Reception design setup', 'description' => 'Modern Design of wedding reception set up'],
        //             ['name' => 'Food cart setup', 'description' => 'Setup food cart choice'],
        //             ['name' => 'Service cleanup', 'description' => 'Clean-up after service completion'],
        //         ]
        //     ],
        //     'HairAndMakeup' => [
        //         '3 Months before' => [
        //             ['name' => 'Initial makeup consultation', 'description' => 'Initial consultations for makeup styles'],
        //             ['name' => 'Theme meeting with couple', 'description' => 'Meeting with the bride and groom to discuss the entourage theme'],
        //             ['name' => 'Prenup hair and makeup', 'description' => 'Prenup Traditional hair and makeup'],
        //         ],
        //         'Wedding Day' => [
        //             ['name' => 'Bride airbrush makeup', 'description' => 'Airbrush makeup for the bride'],
        //             ['name' => 'Groom traditional makeup', 'description' => 'Traditional makeup for the groom'],
        //             ['name' => 'Entourage makeup', 'description' => 'Entourage makeup'],
        //             ['name' => 'Pre-ceremony shoot makeup', 'description' => 'Makeup for pre-ceremony photo and video shoots'],
        //         ],
        //     ],
        //     'PhotoAndVideo' => [
        //         '3 Months before' => [
        //             ['name' => 'Theme discussion for video/album', 'description' => 'Meeting with the couple and coordinator to discuss themes and concepts for video and album'],
        //             ['name' => 'Prenuptial pictorial session', 'description' => 'Prenuptial Pictorial Session'],
        //         ],
        //         '1 Month before' => [
        //             ['name' => 'Confirm shot list', 'description' => 'Confirm shot lists and key moments with the couple and coordinator'],
        //             ['name' => 'Design photo frame', 'description' => 'Designing of Wedding Photo Frame'],
        //         ],
        //         'Wedding Day' => [
        //             ['name' => 'Setup photobooth', 'description' => 'Setup Photobooth'],
        //             ['name' => 'Setup drone', 'description' => 'Setup Aerial Pilot/Drone'],
        //             ['name' => 'Setup projector screen', 'description' => 'Setup Projector Screen'],
        //             ['name' => 'Same-day edit video', 'description' => 'Same day edit video prenuptial photo and engagement session'],
        //             ['name' => 'Same-day photo album edit', 'description' => 'Onside Photo same day edit with photo album with wedding highlights'],
        //             ['name' => 'Raw photo transfer', 'description' => 'Transfer raw photos for clients on the same day']
        //         ],
        //         '6 Months after Wedding Day' => [
        //             ['name' => 'Final file transfer', 'description' => 'File transfer of magnetic wedding album and video to the couple']
        //         ]
        //     ],
        //     'Designing' => [
        //         '9 Months to 6 Months before' => [
        //             ['name' => 'Plan overall theme', 'description' => 'Plan the wedding’s overall theme and identify props to match the motif'],
        //             ['name' => 'Design invitations', 'description' => 'Design and coordinate the invitation cards']
        //         ],  
        //         '3 Months to 1 Month before' => [
        //             ['name' => 'Aisle, walkway, and altar setup', 'description' => 'Finalize aisle setup, walkway, altar, and reception décor'],
        //             ['name' => 'Coordinate reception design', 'description' => 'Coordinate with the catering team for the design of the reception space'],
        //             ['name' => 'Entourage flowers', 'description' => 'Entourage flowers choices Bouquet, Boutonniere, and Corsage'],
        //             ['name' => 'Garden floristry design', 'description' => 'Designing of Garden Floristry'],
        //             ['name' => 'Setup LED wall', 'description' => 'Setup LED Wall']
        //         ],
        //         'Wedding Day' => [
        //             ['name' => 'Early decorative setup', 'description' => 'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor'],
        //             ['name' => 'Final touch-ups', 'description' => 'Final touch-ups on decorative elements before guests arrive']
        //         ]
        //     ],
        //     'Entertainment' => [
        //         '6 Months to 3 Months before' => [
        //             ['name' => 'Reception tone meeting', 'description' => 'Meeting to discuss the tone of the reception (formal or playful)'],
        //             ['name' => 'Plan reception activities', 'description' => 'Plan reception activities based on the couple’s preferences']
        //         ],
        //         '1 Month before' => [
        //             ['name' => 'Check sound and lights', 'description' => 'Check sound and lights equipment'],
        //             ['name' => 'Music preference discussion', 'description' => 'Discuss music preference for both wedding ceremony and reception']
        //         ],
        //         'Wedding Day' => [
        //             ['name' => 'Setup lights and sound', 'description' => 'Setup lights, sound, and channel mixer music operator'],
        //             ['name' => 'Deploy band', 'description' => 'Deploy Acoustic Band'],
        //             ['name' => 'Coordinate with emcee', 'description' => 'Coordinate with the emcee for announcements and flow of events'],
        //             ['name' => 'Setup reception activities', 'description' => 'Set up reception activities like games and first dance']
        //         ]
        //     ]
        // ];
        

        // foreach ($tasks as $departmentName => $categoriesTasks) {
        //     $departmentId = $departments[$departmentName];
        //     foreach ($categoriesTasks as $categoryName => $tasksArray) {
        //         $taskCategoryId = $categories[$categoryName];
        //         foreach ($tasksArray as $task) {
        //             DB::table('tasks')->insert([
        //                 'department_id' => $departmentId,
        //                 'task_category_id' => $taskCategoryId,
        //                 'name' => $task['name'],
        //                 'description' => $task['description'],
        //             ]);
        //         }
        //     }
        // }  oldddddd


        $tasks = [
            'Coordination' => [
                '1 Year to 6 Months before' => [
                    ['name' => 'Initial meeting', 'description' => 'Initial meeting with the couple', 'duration' => 1],
                    ['name' => 'Confirm availability', 'description' => 'Confirm team availability', 'duration' => 7],
                    ['name' => 'Task distribution', 'description' => 'Distribution of tasks and assigning teams', 'duration' => 2],
                    ['name' => 'Memo distribution', 'description' => 'Bring down memo for each team leaders of each department', 'duration' => 7],
                    ['name' => 'Monitor departments', 'description' => 'Monitor each department', 'duration' => 7],
                ],
                '6 Months to 3 Months before' => [
                    ['name' => 'Finalize event flow', 'description' => 'Finalize event flow (Consult with Clients)', 'duration' => 1],
                ],
                '3 Months to 1 Month before' => [
                    ['name' => 'Oversee final prep', 'description' => 'Oversee final preparations and ensure all departments have necessary resources', 'duration' => 2],
                ],
                '1 Week before and Wedding Day' => [
                    ['name' => 'Final check-ins', 'description' => 'Final check-ins with departments', 'duration' => 3],
                    ['name' => 'Day-of oversight', 'description' => 'Day-of oversight, monitoring each department’s progress', 'duration' => 6],
                ],
            ],
            'Catering' => [
                '9 Months to 6 Months before' => [
                    ['name' => 'Food planning meeting', 'description' => 'Meeting for food planning', 'duration' => 1],
                    ['name' => 'Menu planning', 'description' => 'Plan and design the wedding menu', 'duration' => 30],
                ],
                '6 Months to 3 Months before' => [
                    ['name' => 'Food tasting', 'description' => 'Conduct food tasting with the couple', 'duration' => 2],
                    ['name' => 'Coordinate venue logistics', 'description' => 'Coordinate logistics with the venue for catering', 'duration' => 7],
                    ['name' => 'Table and chair setup plan', 'description' => 'Plan chair and table setup to match the wedding’s color theme', 'duration' => 7],
                ],
                '1 Month before' => [
                    ['name' => 'Finalize setup plan', 'description' => 'Finalize setup plan with the designing team for the reception', 'duration' => 3],
                    ['name' => 'Discuss food cart options', 'description' => 'Discuss additional food cart (Mobile Bar or Donut Wall)', 'duration' => 1],
                ],
                'Wedding Day' => [
                    ['name' => 'Buffet Setup', 'description' => 'Buffet Setup', 'duration' => 1],
                    ['name' => 'Reception design setup', 'description' => 'Modern Design of wedding reception set up', 'duration' => 1],
                    ['name' => 'Food cart setup', 'description' => 'Setup food cart choice', 'duration' => 1],
                    ['name' => 'Service cleanup', 'description' => 'Clean-up after service completion', 'duration' => 1],
                ],
            ],
            'HairAndMakeup' => [
                '3 Months before' => [
                    ['name' => 'Initial makeup consultation', 'description' => 'Initial consultations for makeup styles', 'duration' => 2],
                    ['name' => 'Theme meeting with couple', 'description' => 'Meeting with the bride and groom to discuss the entourage theme', 'duration' => 2],
                    ['name' => 'Prenup hair and makeup', 'description' => 'Prenup Traditional hair and makeup', 'duration' => 3],
                ],
                'Wedding Day' => [
                    ['name' => 'Bride airbrush makeup', 'description' => 'Airbrush makeup for the bride', 'duration' => 1],
                    ['name' => 'Groom traditional makeup', 'description' => 'Traditional makeup for the groom', 'duration' => 1],
                    ['name' => 'Entourage makeup', 'description' => 'Entourage makeup', 'duration' => 1],
                    ['name' => 'Pre-ceremony shoot makeup', 'description' => 'Makeup for pre-ceremony photo and video shoots', 'duration' => 1],
                ],
            ],
            'PhotoAndVideo' => [
                '3 Months before' => [
                    ['name' => 'Theme discussion for video/album', 'description' => 'Meeting with the couple and coordinator to discuss themes and concepts for video and album', 'duration' => 4],
                    ['name' => 'Prenuptial pictorial session', 'description' => 'Prenuptial Pictorial Session', 'duration' => 7],
                ],
                '1 Month before' => [
                    ['name' => 'Confirm shot list', 'description' => 'Confirm shot lists and key moments with the couple and coordinator', 'duration' => 1],
                    ['name' => 'Design photo frame', 'description' => 'Designing of Wedding Photo Frame', 'duration' => 1],
                ],
                'Wedding Day' => [
                    ['name' => 'Setup photobooth', 'description' => 'Setup Photobooth', 'duration' => 1],
                    ['name' => 'Setup drone', 'description' => 'Setup Aerial Pilot/Drone', 'duration' => 1],
                    ['name' => 'Setup projector screen', 'description' => 'Setup Projector Screen', 'duration' => 1],
                    ['name' => 'Same-day edit video', 'description' => 'Same day edit video prenuptial photo and engagement session', 'duration' => 1],
                    ['name' => 'Same-day photo album edit', 'description' => 'Onside Photo same day edit with photo album with wedding highlights', 'duration' => 1],
                    ['name' => 'Raw photo transfer', 'description' => 'Transfer raw photos for clients on the same day', 'duration' => 1],
                ],
                '6 Months after Wedding Day' => [
                    ['name' => 'Final file transfer', 'description' => 'File transfer of magnetic wedding album and video to the couple', 'duration' => 1],
                ],
            ],
            'Designing' => [
                '9 Months to 6 Months before' => [
                    ['name' => 'Plan overall theme', 'description' => 'Plan the wedding’s overall theme and identify props to match the motif', 'duration' => 30],
                    ['name' => 'Design invitations', 'description' => 'Design and coordinate the invitation cards', 'duration' => 7],
                ],
                '3 Months to 1 Month before' => [
                    ['name' => 'Aisle, walkway, and altar setup', 'description' => 'Finalize aisle setup, walkway, altar, and reception décor', 'duration' => 3],
                    ['name' => 'Coordinate reception design', 'description' => 'Coordinate with the catering team for the design of the reception space', 'duration' => 3],
                    ['name' => 'Entourage flowers', 'description' => 'Entourage flowers choices Bouquet, Boutonniere, and Corsage', 'duration' => 2],
                    ['name' => 'Garden floristry design', 'description' => 'Designing of Garden Floristry', 'duration' => 2],
                    ['name' => 'Setup LED wall', 'description' => 'Setup LED Wall', 'duration' => 1],
                ],
                'Wedding Day' => [
                    ['name' => 'Early decorative setup', 'description' => 'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor', 'duration' => 1],
                    ['name' => 'Final touch-ups', 'description' => 'Final touch-ups on decorative elements before guests arrive', 'duration' => 1],
                ],
            ],
            'Entertainment' => [
                '6 Months to 3 Months before' => [
                    ['name' => 'Reception tone meeting', 'description' => 'Meeting to discuss the tone of the reception (formal or playful)', 'duration' => 1],
                    ['name' => 'Plan reception activities', 'description' => 'Plan reception activities based on the couple’s preferences', 'duration' => 7],
                ],
                '1 Month before' => [
                    ['name' => 'Check sound and lights', 'description' => 'Check sound and lights equipment', 'duration' => 1],
                    ['name' => 'Music preference discussion', 'description' => 'Discuss music preference for both wedding ceremony and reception', 'duration' => 1],
                ],
                'Wedding Day' => [
                    ['name' => 'Setup lights and sound', 'description' => 'Setup lights, sound, and channel mixer music operator', 'duration' => 1],
                    ['name' => 'Deploy band', 'description' => 'Deploy Acoustic Band', 'duration' => 1],
                    ['name' => 'Coordinate with emcee', 'description' => 'Coordinate with the emcee for announcements and flow of events', 'duration' => 1],
                    ['name' => 'Setup reception activities', 'description' => 'Set up reception activities like games and first dance', 'duration' => 1],
                ],
            ],
        ];
        
        foreach ($tasks as $departmentName => $categoriesTasks) {
            $departmentId = $departments[$departmentName];
            foreach ($categoriesTasks as $categoryName => $tasksArray) {
                $taskCategoryId = $categories[$categoryName];
                foreach ($tasksArray as $task) {
                    DB::table('tasks')->insert([
                        'department_id' => $departmentId,
                        'task_category_id' => $taskCategoryId,
                        'name' => $task['name'],
                        'description' => $task['description'],
                        'duration' => $task['duration'], // Duration added here
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
            ['package_id' => 2 , 'task_id' => 17],
            ['package_id' => 2 , 'task_id' => 18],
            ['package_id' => 2 , 'task_id' => 20],
            ['package_id' => 2 , 'task_id' => 21],
            ['package_id' => 2 , 'task_id' => 22],
            ['package_id' => 2 , 'task_id' => 24],
            ['package_id' => 2 , 'task_id' => 26],
            ['package_id' => 2 , 'task_id' => 27],
            ['package_id' => 2 , 'task_id' => 28],
            ['package_id' => 2 , 'task_id' => 30],
            ['package_id' => 2 , 'task_id' => 33],
            ['package_id' => 2 , 'task_id' => 34],
            ['package_id' => 2 , 'task_id' => 35],
            ['package_id' => 2 , 'task_id' => 38],
            ['package_id' => 2 , 'task_id' => 39],
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
            ['package_id' => 1 , 'task_id' => 17],
            ['package_id' => 1 , 'task_id' => 18],
            ['package_id' => 1 , 'task_id' => 20],
            ['package_id' => 1 , 'task_id' => 21],
            ['package_id' => 1 , 'task_id' => 22],
            ['package_id' => 1 , 'task_id' => 24],
            ['package_id' => 1 , 'task_id' => 26],
            ['package_id' => 1 , 'task_id' => 27],
            ['package_id' => 1 , 'task_id' => 39],
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
