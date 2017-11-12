<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Seed30Inspection extends AbstractSeed
{
    public function run()
    {
        $inspections = $this->table('inspection');
        $ghInspections = $this->table('github_pr_inspection');

        $inspections->insert([
            [
                'id' => 'fca728fc-9be0-40a1-a7d3-94f3fe4e118a',
                'report_id' => null,
                'created_at' => '2017-11-08 21:36:00',
                'started_at' => null,
                'finished_at' => null,
                'base' => '61e5db566c1eb828795ba41f531d675e6a084445',
                'head' => '34831fc20b1dbc08f6c8642331df6c9d76772bfa',
                'status' => 'scheduled',
                'type' => 'github_pr',
                'failure_trace' => '',
            ],
        ]);

        $ghInspections->insert([
            [
                'id' => 'f733e45a-6fc7-404b-879d-656d68e0498d',
                'pull_request_number' => 14,
                'repository_id' => '2017-11-08 21:36:00',
            ],
        ]);

        $inspections->save();
        $ghInspections->save();
    }
}
