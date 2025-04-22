<?php

namespace App\Services;

use App\Models\Package;
use App\Services\TrelloPackage;
use Illuminate\Support\Facades\Log;


class PackageService
{
    private $package;
    private $trelloPackage;

    public function __construct(
        Package $package,
        TrelloPackage $trelloPackage
    ) {
        $this->package = $package;
        $this->trelloPackage = $trelloPackage;
    }

    public function createPackageBoard(Package $package)
    {
        Log::info('Creating a new trello board for package: ', ['package_name' => $package->name]);

        $boardResponse = $this->trelloPackage->createPackageBoard($package->name);

        if ($boardResponse && isset($boardResponse['id'])) {
            $package->trello_board_template_id = $boardResponse['id'];
            $package->save();
            Log::info('Trello board created with ID: ' . $boardResponse['id']);

            $this->trelloPackage->createList($boardResponse['id'], 'Departments');
            $this->trelloPackage->createList($boardResponse['id'], 'Coordinators');
            $projectDetailsList = $this->trelloPackage->createList($boardResponse['id'], 'Project details');

            if ($projectDetailsList && isset($projectDetailsList['id'])) {
                $this->trelloPackage->createCard($projectDetailsList['id'], 'name of couple');
                $this->trelloPackage->createCard($projectDetailsList['id'], 'package');
                $this->trelloPackage->createCard($projectDetailsList['id'], 'description');
                $this->trelloPackage->createCard($projectDetailsList['id'], 'special request');
                $this->trelloPackage->createCard($projectDetailsList['id'], 'venue of wedding');
                $this->trelloPackage->createCard($projectDetailsList['id'], 'wedding theme color');
            }
        }

        return $package;
    }

    public function deletePackageBoard(Package $package)
    {
        Log::info('Deleting Trello board for package: ' . $package->name);

        $this->trelloPackage->deletePackageBoard($package->trello_board_template_id);

        Log::info('Trello board deleted for package: ' . $package->name);
    }
}
