<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddFlightModeToRepositories extends AbstractMigration
{
    public function change()
    {
        $repositories = $this->table('repository', ['id' => false, 'primary_key' => ['id']]);

        $repositories
            ->addColumn('is_flight_mode_enabled', 'boolean', ['default' => false])
        ;

        $repositories->save();
    }
}
