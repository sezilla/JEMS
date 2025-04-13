<?php

return [

    'scheduling' => [
        'id' => 1,
        'name' => 'Scheduling',
        'description' => 'Organizing and setting time frames for tasks and events.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'communication' => [
        'id' => 2,
        'name' => 'Communication',
        'description' => 'Effectively conveying information and ideas.',
        'departments' => [
            config('seeder.department.coordination.id'),
            config('seeder.department.catering.id'),
            config('seeder.department.hair_and_makeup.id'),
            config('seeder.department.designing.id')
        ],
    ],

    'organization' => [
        'id' => 3,
        'name' => 'Organization',
        'description' => 'Arranging resources and tasks in a systematic manner.',
        'departments' => [
            config('seeder.department.coordination.id'),
            config('seeder.department.catering.id'),
            config('seeder.department.photo_and_video.id')
        ],
    ],

    'attention-to-detail' => [
        'id' => 4,
        'name' => 'Attention to Detail',
        'description' => 'Focusing on small aspects and ensuring nothing is overlooked.',
        'departments' => [
            config('seeder.department.coordination.id'),
            config('seeder.department.catering.id'),
            config('seeder.department.entertainment.id')
        ],
    ],

    'delegation' => [
        'id' => 5,
        'name' => 'Delegation',
        'description' => 'Assigning tasks and responsibilities to others.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'leadership' => [
        'id' => 6,
        'name' => 'Leadership',
        'description' => 'Guiding and motivating a team to achieve goals.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'documentation' => [
        'id' => 7,
        'name' => 'Documentation',
        'description' => 'Creating, maintaining, and organizing written records and materials.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'oversight' => [
        'id' => 8,
        'name' => 'Oversight',
        'description' => 'Monitoring tasks, processes, or teams to ensure success and compliance.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'time-management' => [
        'id' => 9,
        'name' => 'Time Management',
        'description' => 'Prioritizing tasks and managing time effectively.',
        'departments' => [
            config('seeder.department.coordination.id'),
            config('seeder.department.catering.id'),
            config('seeder.department.hair_and_makeup.id'),
            config('seeder.department.photo_and_video.id')
        ],
    ],

    'reporting' => [
        'id' => 10,
        'name' => 'Reporting',
        'description' => 'Providing regular updates and analyses on activities and outcomes.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'planning' => [
        'id' => 11,
        'name' => 'Planning',
        'description' => 'Strategizing and organizing tasks, goals, and resources.',
        'departments' => [
            config('seeder.department.coordination.id'),
            config('seeder.department.designing.id')
        ],
    ],

    'organizational-skills' => [
        'id' => 12,
        'name' => 'Organizational Skills',
        'description' => 'Ability to arrange tasks and resources efficiently.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'adaptability' => [
        'id' => 13,
        'name' => 'Adaptability',
        'description' => 'Adjusting to changing circumstances and challenges.',
        'departments' => [
            config('seeder.department.coordination.id'),
            config('seeder.department.hair_and_makeup.id')
        ],
    ],

    'problem-solving' => [
        'id' => 14,
        'name' => 'Problem-Solving',
        'description' => 'Identifying issues and finding effective solutions.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'coordination' => [
        'id' => 15,
        'name' => 'Coordination',
        'description' => 'Ensuring different elements work together effectively.',
        'departments' => [
            config('seeder.department.coordination.id'),
            config('seeder.department.catering.id')
        ],
    ],

    'crisis-management' => [
        'id' => 16,
        'name' => 'Crisis Management',
        'description' => 'Managing unexpected events or emergencies.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'multitasking' => [
        'id' => 17,
        'name' => 'Multitasking',
        'description' => 'Handling multiple tasks simultaneously with focus and effectiveness.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'menu-planning' => [
        'id' => 18,
        'name' => 'Menu Planning',
        'description' => 'Designing meal options or event menus.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'culinary-knowledge' => [
        'id' => 19,
        'name' => 'Culinary Knowledge',
        'description' => 'Understanding ingredients, cooking techniques, and food safety.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'creativity' => [
        'id' => 20,
        'name' => 'Creativity',
        'description' => 'Thinking outside the box and coming up with innovative ideas.',
        'departments' => [
            config('seeder.department.catering.id'),
            config('seeder.department.hair_and_makeup.id'),
            config('seeder.department.photo_and_video.id'),
            config('seeder.department.designing.id'),
            config('seeder.department.entertainment.id')
        ],
    ],

    'quality-assessment' => [
        'id' => 21,
        'name' => 'Quality Assessment',
        'description' => 'Evaluating products or services to ensure they meet standards.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'sensory-evaluation' => [
        'id' => 22,
        'name' => 'Sensory Evaluation',
        'description' => 'Assessing qualities like taste, smell, texture, and appearance.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'logistics' => [
        'id' => 23,
        'name' => 'Logistics',
        'description' => 'Managing the movement of goods, services, and information.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'space-planning' => [
        'id' => 24,
        'name' => 'Space Planning',
        'description' => 'Designing efficient layouts for spaces and resources.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'design-sense' => [
        'id' => 25,
        'name' => 'Design Sense',
        'description' => 'Aesthetic awareness and ability to create appealing designs.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'flexibility' => [
        'id' => 26,
        'name' => 'Flexibility',
        'description' => 'Being open to changes and new ideas.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'guest-focus' => [
        'id' => 27,
        'name' => 'Guest Focus',
        'description' => 'Prioritizing guest satisfaction and experience.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'budgeting' => [
        'id' => 28,
        'name' => 'Budgeting',
        'description' => 'Creating and managing financial plans and expenditures.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'customer-service' => [
        'id' => 29,
        'name' => 'Customer Service',
        'description' => 'Assisting and meeting the needs of customers effectively.',
        'departments' => [config('seeder.department.catering.id')],
    ],

    'interior-design' => [
        'id' => 30,
        'name' => 'Interior Design',
        'description' => 'Creating aesthetically pleasing and functional indoor spaces.',
        'departments' => [config('seeder.department.hair_and_makeup.id')],
    ],

    'professionalism' => [
        'id' => 31,
        'name' => 'Professionalism',
        'description' => 'Exhibiting expertise, behavior, and a positive attitude in work.',
        'departments' => [
            config('seeder.department.hair_and_makeup.id'),
            config('seeder.department.photo_and_video.id'),
            config('seeder.department.designing.id')
        ],
    ],

    'listening-skills' => [
        'id' => 32,
        'name' => 'Listening Skills',
        'description' => 'Paying attention to and understanding what others are saying.',
        'departments' => [config('seeder.department.hair_and_makeup.id')],
    ],

    'artistic-skills' => [
        'id' => 33,
        'name' => 'Artistic Skills',
        'description' => 'Creating visual art or performing arts with skill and creativity.',
        'departments' => [config('seeder.department.hair_and_makeup.id')],
    ],

    'advanced-makeup-techniques' => [
        'id' => 34,
        'name' => 'Advanced Makeup Techniques',
        'description' => 'Applying makeup with high-level techniques for various purposes.',
        'departments' => [config('seeder.department.hair_and_makeup.id')],
    ],

    'precision' => [
        'id' => 35,
        'name' => 'Precision',
        'description' => 'Performing tasks with exactness and accuracy.',
        'departments' => [config('seeder.department.hair_and_makeup.id')],
    ],

    'basic-makeup-skills' => [
        'id' => 36,
        'name' => 'Basic Makeup Skills',
        'description' => 'Applying makeup with fundamental techniques and knowledge.',
        'departments' => [config('seeder.department.hair_and_makeup.id')],
    ],

    'grooming-knowledge' => [
        'id' => 37,
        'name' => 'Grooming Knowledge',
        'description' => 'Understanding grooming practices for personal care.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'versatility' => [
        'id' => 38,
        'name' => 'Versatility',
        'description' => 'Ability to adapt to a variety of tasks and environments.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'quick-setup' => [
        'id' => 39,
        'name' => 'Quick Setup',
        'description' => 'Setting up equipment or tasks in an efficient and fast manner.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'storytelling' => [
        'id' => 40,
        'name' => 'Storytelling',
        'description' => 'Conveying narratives in an engaging and creative manner.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'photography' => [
        'id' => 41,
        'name' => 'Photography',
        'description' => 'Capturing images using cameras and other photographic equipment.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'aesthetic-sense' => [
        'id' => 42,
        'name' => 'Aesthetic Sense',
        'description' => 'Having an eye for beauty and design in various forms.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'technical-skills' => [
        'id' => 43,
        'name' => 'Technical Skills',
        'description' => 'Proficiency in using tools, technology, or technical systems.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'drone-operation' => [
        'id' => 44,
        'name' => 'Drone Operation',
        'description' => 'Controlling drones for various tasks, including aerial photography.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'av-skills' => [
        'id' => 45,
        'name' => 'AV Skills',
        'description' => 'Operating and troubleshooting audio-visual equipment.',
        'departments' => [
            config('seeder.department.photo_and_video.id'),
            config('seeder.department.entertainment.id')
        ],
    ],

    'technical-setup' => [
        'id' => 46,
        'name' => 'Technical Setup',
        'description' => 'Arranging technical equipment for events or tasks.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'video-editing' => [
        'id' => 47,
        'name' => 'Video Editing',
        'description' => 'Editing video content for quality and presentation.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'photo-editing' => [
        'id' => 48,
        'name' => 'Photo Editing',
        'description' => 'Enhancing and modifying photos for better visual appeal.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'efficiency' => [
        'id' => 49,
        'name' => 'Efficiency',
        'description' => 'Completing tasks in the least amount of time and resources.',
        'departments' => [config('seeder.department.entertainment.id')],
    ],

    'data-handling' => [
        'id' => 50,
        'name' => 'Data Handling',
        'description' => 'Managing, storing, and processing data effectively.',
        'departments' => [config('seeder.department.entertainment.id')],
    ],

    'design-knowledge' => [
        'id' => 51,
        'name' => 'Design Knowledge',
        'description' => 'Understanding design principles and aesthetics.',
        'departments' => [config('seeder.department.entertainment.id')],
    ],

    'graphic-design' => [
        'id' => 52,
        'name' => 'Graphic Design',
        'description' => 'Creating visual content using design software and artistic principles.',
        'departments' => [config('seeder.department.entertainment.id')],
    ],

    'typography' => [
        'id' => 53,
        'name' => 'Typography',
        'description' => 'Working with fonts and text layout to create readable, attractive designs.',
        'departments' => [config('seeder.department.entertainment.id')],
    ],

    'spatial-design' => [
        'id' => 54,
        'name' => 'Spatial Design',
        'description' => 'Designing spaces and layouts with attention to functionality and aesthetics.',
        'departments' => [config('seeder.department.entertainment.id')],
    ],

    'project-coordination' => [
        'id' => 55,
        'name' => 'Project Coordination',
        'description' => 'Organizing tasks and people to achieve a projects objectives.',
        'departments' => [config('seeder.department.entertainment.id')],
    ],

    'floral-arrangement' => [
        'id' => 56,
        'name' => 'Floral Arrangement',
        'description' => 'Designing and arranging flowers for various occasions.',
        'departments' => [config('seeder.department.designing.id')],
    ],

    'gardening' => [
        'id' => 57,
        'name' => 'Gardening',
        'description' => 'Cultivating and maintaining plants in gardens or landscapes.',
        'departments' => [config('seeder.department.designing.id')],
    ],

    'project-planning' => [
        'id' => 58,
        'name' => 'Project Planning',
        'description' => 'Creating a structured approach for achieving project goals.',
        'departments' => [config('seeder.department.designing.id')],
    ],

    'finishing-skills' => [
        'id' => 59,
        'name' => 'Finishing Skills',
        'description' => 'Applying final touches to a product or project for completion.',
        'departments' => [config('seeder.department.designing.id')],
    ],

    'event-planning' => [
        'id' => 60,
        'name' => 'Event Planning',
        'description' => 'Coordinating all aspects of events from start to finish.',
        'departments' => [config('seeder.department.designing.id')],
    ],

    'audio-visual-knowledge' => [
        'id' => 61,
        'name' => 'Audio-Visual Knowledge',
        'description' => 'Understanding and using audio-visual equipment and technology.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'music-knowledge' => [
        'id' => 62,
        'name' => 'Music Knowledge',
        'description' => 'Understanding music theory, genres, and performance techniques.',
        'departments' => [config('seeder.department.photo_and_video.id')],
    ],

    'event-management' => [
        'id' => 63,
        'name' => 'Event Management',
        'description' => 'Overseeing the organization and execution of events.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

    'event-coordination' => [
        'id' => 64,
        'name' => 'Event Coordination',
        'description' => 'Handling the logistics and details of an event for smooth execution.',
        'departments' => [config('seeder.department.coordination.id')],
    ],

];
