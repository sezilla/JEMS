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
            'slug' => 'initial-meeting',
            'name' => 'Initial meeting',
            'description' => 'Initial meeting with the couple',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'confirm-availability',
            'name' => 'Confirm availability',
            'description' => 'Confirm team availability',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'task-distribution',
            'name' => 'Task distribution',
            'description' => 'Distribution of tasks and assigning teams',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'memo-distribution',
            'name' => 'Memo distribution',
            'description' => 'Bring down memo for each team leaders of each department',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'monitor-departments',
            'name' => 'Monitor departments',
            'description' => 'Monitor each department',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1year_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'finalize-event-flow',
            'name' => 'Finalize event flow',
            'description' => 'Finalize event flow (Consult with Clients)',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'oversee-final-prep',
            'name' => 'Oversee final prep',
            'description' => 'Oversee final preparations and ensure all departments have necessary resources',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'final-check-ins',
            'name' => 'Final check-ins',
            'description' => 'Final check-ins with departments',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1week.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'day-of-oversight',
            'name' => 'Day-of oversight',
            'description' => 'Day-of oversight, monitoring each departments progress',
            'department_id' => config('seeder.department.coordination.id'),
            'task_category_id' => config('seeder.taskCategory.1week.id'),
        ]);

        // Catering Department Tasks
        Task::updateOrCreate([
            'slug' => 'food-planning-meeting',
            'name' => 'Food planning meeting',
            'description' => 'Meeting for food planning',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'menu-planning',
            'name' => 'Menu planning',
            'description' => 'Plan and design the wedding menu',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'food-tasting',
            'name' => 'Food tasting',
            'description' => 'Conduct food tasting with the couple',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'coordinate-venue-logistics',
            'name' => 'Coordinate venue logistics',
            'description' => 'Coordinate logistics with the venue for catering',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'table-and-chair-setup-plan',
            'name' => 'Table and chair setup plan',
            'description' => 'Plan chair and table setup to match the weddings color theme',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'finalize-setup-plan',
            'name' => 'Finalize setup plan',
            'description' => 'Finalize setup plan with the designing team for the reception',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'discuss-food-cart-options',
            'name' => 'Discuss food cart options',
            'description' => 'Discuss additional food cart (Mobile Bar or Donut Wall)',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'buffet-setup',
            'name' => 'Buffet Setup',
            'description' => 'Buffet Setup',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'reception-design-setup',
            'name' => 'Reception design setup',
            'description' => 'Modern Design of wedding reception set up',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'food-cart-setup',
            'name' => 'Food cart setup',
            'description' => 'Setup food cart choice',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'service-cleanup',
            'name' => 'Service cleanup',
            'description' => 'Clean-up after service completion',
            'department_id' => config('seeder.department.catering.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);

        // Hair and Makeup Department Tasks
        Task::updateOrCreate([
            'slug' => 'initial-makeup-consultation',
            'name' => 'Initial makeup consultation',
            'description' => 'Initial consultations for makeup styles',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'theme-meeting-with-couple',
            'name' => 'Theme meeting with couple',
            'description' => 'Meeting with the bride and groom to discuss the entourage theme',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'prenup-hair-and-makeup',
            'name' => 'Prenup hair and makeup',
            'description' => 'Prenup Traditional hair and makeup',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'bride-airbrush-makeup',
            'name' => 'Bride airbrush makeup',
            'description' => 'Airbrush makeup for the bride',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'groom-traditional-makeup',
            'name' => 'Groom traditional makeup',
            'description' => 'Traditional makeup for the groom',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'entourage-makeup',
            'name' => 'Entourage makeup',
            'description' => 'Entourage makeup',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'pre-ceremony-shoot-makeup',
            'name' => 'Pre-ceremony shoot makeup',
            'description' => 'Makeup for pre-ceremony photo and video shoots',
            'department_id' => config('seeder.department.hair_and_makeup.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);

        // Photo and Video Department Tasks
        Task::updateOrCreate([
            'slug' => 'theme-discussion-for-video-album',
            'name' => 'Theme discussion for video/album',
            'description' => 'Meeting with the couple and coordinator to discuss themes and concepts for video and album',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'prenuptial-pictorial-session',
            'name' => 'Prenuptial pictorial session',
            'description' => 'Prenuptial Pictorial Session',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.4months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'confirm-shot-list',
            'name' => 'Confirm shot list',
            'description' => 'Confirm shot lists and key moments with the couple and coordinator',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'design-photo-frame',
            'name' => 'Design photo frame',
            'description' => 'Designing of Wedding Photo Frame',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'setup-photobooth',
            'name' => 'Setup photobooth',
            'description' => 'Setup Photobooth',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'setup-drone',
            'name' => 'Setup drone',
            'description' => 'Setup Aerial Pilot/Drone',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'setup-projector-screen',
            'name' => 'Setup projector screen',
            'description' => 'Setup Projector Screen',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'same-day-edit-video',
            'name' => 'Same-day edit video',
            'description' => 'Same day edit video prenuptial photo and engagement session',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'same-day-photo-album-edit',
            'name' => 'Same-day photo album edit',
            'description' => 'Onside Photo same day edit with photo album with wedding highlights',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'raw-photo-transfer',
            'name' => 'Raw photo transfer',
            'description' => 'Transfer raw photos for clients on the same day',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'final-file-transfer',
            'name' => 'Final file transfer',
            'description' => 'File transfer of magnetic wedding album and video to the couple',
            'department_id' => config('seeder.department.photo_and_video.id'),
            'task_category_id' => config('seeder.taskCategory.6months_after.id'),
        ]);

        // Designing Department Tasks
        Task::updateOrCreate([
            'slug' => 'plan-overall-theme',
            'name' => 'Plan overall theme',
            'description' => 'File transfer of magnetic wedding album and video to the couple',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'design-invitation',
            'name' => 'Design invitation',
            'description' => 'Design wedding invitation',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.9months_to_6months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'aisle-walkway-setup',
            'name' => 'Aisle, walkway, and altar setup',
            'description' => 'Design wedding setup',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'coordinate-reception-design',
            'name' => 'Coordinate reception design',
            'description' => 'Coordinate reception design with catering',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'entourage-flowers',
            'name' => 'Entourage flowers',
            'description' => 'entourage flowers',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'garden-forestry-design',
            'name' => 'Garden forestry design',
            'description' => 'Design and setup garden forestry for the wedding venue',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.3months_to_1month.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'setup-led-wall',
            'name' => 'Setup LED wall',
            'description' => 'Setup LED wall for visual presentations and effects',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'early-decorative-setup',
            'name' => 'Early decorative setup',
            'description' => 'Early morning venue decoration setup',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'final-touch-ups',
            'name' => 'Final touch-ups',
            'description' => 'Last minute decoration adjustments',
            'department_id' => config('seeder.department.designing.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);

        // Entertainment Tasks
        Task::updateOrCreate([
            'slug' => 'reception-tone-meeting',
            'name' => 'Reception tone meeting',
            'description' => 'Meeting to discuss the tone and style of the reception',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'plan-reception-activities',
            'name' => 'Plan reception activities',
            'description' => 'Plan entertainment activities for the reception',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.6months_to_3months.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'check-sound-and-lights',
            'name' => 'Check sound and lights',
            'description' => 'Verify all audio and lighting equipment is functional',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'music-preference-discussion',
            'name' => 'Music preference discussion',
            'description' => 'Discussion about music selections for ceremony and reception',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.1month_to_1week.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'setup-lights-and-sound',
            'name' => 'Setup lights and sound',
            'description' => 'Install and test all lighting and sound equipment',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'deploy-band',
            'name' => 'Deploy band',
            'description' => 'Set up band/DJ equipment and conduct sound check',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'coordinate-with-emcee',
            'name' => 'Coordinate with emcee',
            'description' => 'Brief the master of ceremonies about the program flow',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
        Task::updateOrCreate([
            'slug' => 'setup-reception-activities',
            'name' => 'Setup reception activities',
            'description' => 'Prepare all planned reception entertainment activities',
            'department_id' => config('seeder.department.entertainment.id'),
            'task_category_id' => config('seeder.taskCategory.day_of.id'),
        ]);
    }
}
