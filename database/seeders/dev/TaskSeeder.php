<?php

namespace Database\Seeders\dev;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Coordination Department Tasks
        Task::updateOrCreate([
            'name' => 'Initial meeting',
            'description' => 'Initial meeting with the couple',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Confirm availability',
            'description' => 'Confirm team availability',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Task distribution',
            'description' => 'Distribution of tasks and assigning teams',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Memo distribution',
            'description' => 'Bring down memo for each team leaders of each department',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Monitor departments',
            'description' => 'Monitor each department',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Finalize event flow',
            'description' => 'Finalize event flow (Consult with Clients)',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Oversee final prep',
            'description' => 'Oversee final preparations and ensure all departments have necessary resources',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Final check-ins',
            'description' => 'Final check-ins with departments',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1week.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Day-of oversight',
            'description' => 'Day-of oversight, monitoring each departments progress',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1week.id'),
        ]);

        // Catering Department Tasks
        Task::updateOrCreate([
            'name' => 'Food planning meeting',
            'description' => 'Meeting for food planning',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Menu planning',
            'description' => 'Plan and design the wedding menu',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Food tasting',
            'description' => 'Conduct food tasting with the couple',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Coordinate venue logistics',
            'description' => 'Coordinate logistics with the venue for catering',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Table and chair setup plan',
            'description' => 'Plan chair and table setup to match the weddings color theme',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Finalize setup plan',
            'description' => 'Finalize setup plan with the designing team for the reception',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Discuss food cart options',
            'description' => 'Discuss additional food cart (Mobile Bar or Donut Wall)',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Buffet Setup',
            'description' => 'Buffet Setup',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Reception design setup',
            'description' => 'Modern Design of wedding reception set up',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Food cart setup',
            'description' => 'Setup food cart choice',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Service cleanup',
            'description' => 'Clean-up after service completion',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);

        // Hair and Makeup Department Tasks
        Task::updateOrCreate([
            'name' => 'Initial makeup consultation',
            'description' => 'Initial consultations for makeup styles',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Theme meeting with couple',
            'description' => 'Meeting with the bride and groom to discuss the entourage theme',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Prenup hair and makeup',
            'description' => 'Prenup Traditional hair and makeup',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Bride airbrush makeup',
            'description' => 'Airbrush makeup for the bride',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Groom traditional makeup',
            'description' => 'Traditional makeup for the groom',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Entourage makeup',
            'description' => 'Entourage makeup',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Pre-ceremony shoot makeup',
            'description' => 'Makeup for pre-ceremony photo and video shoots',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);

        // Photo and Video Department Tasks
        Task::updateOrCreate([
            'name' => 'Theme discussion for video/album',
            'description' => 'Meeting with the couple and coordinator to discuss themes and concepts for video and album',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Prenuptial pictorial session',
            'description' => 'Prenuptial Pictorial Session',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Confirm shot list',
            'description' => 'Confirm shot lists and key moments with the couple and coordinator',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Design photo frame',
            'description' => 'Designing of Wedding Photo Frame',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Setup photobooth',
            'description' => 'Setup Photobooth',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Setup drone',
            'description' => 'Setup Aerial Pilot/Drone',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Setup projector screen',
            'description' => 'Setup Projector Screen',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Same-day edit video',
            'description' => 'Same day edit video prenuptial photo and engagement session',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Same-day photo album edit',
            'description' => 'Onside Photo same day edit with photo album with wedding highlights',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Raw photo transfer',
            'description' => 'Transfer raw photos for clients on the same day',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Final file transfer',
            'description' => 'File transfer of magnetic wedding album and video to the couple',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.6months_after.id'),
        ]);

        // Designing Department Tasks
        Task::updateOrCreate([
            'name' => 'Plan overall theme',
            'description' => 'Plan the weddings overall theme and identify props to match the motif',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Design invitations',
            'description' => 'Design and coordinate the invitation cards',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Aisle, walkway, and altar setup',
            'description' => 'Finalize aisle setup, walkway, altar, and reception décor',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Coordinate reception design',
            'description' => 'Coordinate with the catering team for the design of the reception space',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Entourage flowers',
            'description' => 'Entourage flowers choices Bouquet, Boutonniere, and Corsage',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Garden floristry design',
            'description' => 'Designing of Garden Floristry',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Setup LED wall',
            'description' => 'Setup LED Wall',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Early decorative setup',
            'description' => 'Early setup of all decorative elements, including aisle flowers, altar, entrance arch, and reception decor',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Final touch-ups',
            'description' => 'Final touch-ups on decorative elements before guests arrive',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);

        // Entertainment Department Tasks
        Task::updateOrCreate([
            'name' => 'Reception tone meeting',
            'description' => 'Meeting to discuss the tone of the reception (formal or playful)',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Plan reception activities',
            'description' => 'Plan reception activities based on the couples preferences',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Check sound and lights',
            'description' => 'Check sound and lights equipment',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Music preference discussion',
            'description' => 'Discuss music preference for both wedding ceremony and reception',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Setup lights and sound',
            'description' => 'Setup lights, sound, and channel mixer music operator',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Deploy band',
            'description' => 'Deploy Acoustic Band',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Coordinate with emcee',
            'description' => 'Coordinate with the emcee for announcements and flow of events',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'name' => 'Setup reception activities',
            'description' => 'Set up reception activities like games and first dance',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
    }
}
