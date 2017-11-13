<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class BitbucketPrInspectionTable extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function change()
    {
        $bitbucketPrInspections = $this->table('bitbucket_pr_inspection', ['id' => false, 'primary_key' => ['id']]);

        $bitbucketPrInspections
            ->addColumn('id', 'uuid')
            ->addColumn('pull_request_number', 'integer')
            ->addColumn('repository_id', 'uuid')
            ->addForeignKey('id', 'inspection', 'id')
            ->addForeignKey('repository_id', 'repository', 'id')
        ;

        $bitbucketPrInspections->create();
    }
}
