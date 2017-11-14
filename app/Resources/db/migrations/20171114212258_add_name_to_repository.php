<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddNameToRepository extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function change()
    {
        $repositories = $this->table('repository', ['id' => false, 'primary_key' => ['id']]);

        $repositories->addColumn('name', 'string');

        $repositories->save();
    }
}
