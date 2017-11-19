<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Seed20Repository extends AbstractSeed
{
    public function run()
    {
        $repositories = $this->table('repository');

        $repositories->insert([
            [
                'id' => 'f733e45a-6fc7-404b-879d-656d68e0498d',
                'identifier' => 'K-Phoen/regis',
                'type' => 'github',
                'name' => 'K-Phoen/regis',
                'owner_id' => 'd67ff369-704b-4315-a75f-b67f5bc9cc5a',
                'shared_secret' => 'some-shared-secret',
                'is_inspection_enabled' => true,
            ],
        ]);

        $repositories->save();
    }
}
