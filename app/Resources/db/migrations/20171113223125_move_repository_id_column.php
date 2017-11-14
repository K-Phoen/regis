<?php


use Phinx\Migration\AbstractMigration;

class MoveRepositoryIdColumn extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function change()
    {
        $githubPrInspections = $this->table('github_pr_inspection', ['id' => false, 'primary_key' => ['id']]);
        $bitbucketPrInspections = $this->table('bitbucket_pr_inspection', ['id' => false, 'primary_key' => ['id']]);
        $inspections = $this->table('inspection', ['id' => false, 'primary_key' => ['id']]);

        $inspections
            ->addColumn('repository_id', 'uuid')
            ->addForeignKey('repository_id', 'repository', 'id')
        ;

        $githubPrInspections->removeColumn('repository_id');
        $bitbucketPrInspections->removeColumn('repository_id');

        $bitbucketPrInspections->save();
        $githubPrInspections->save();
        $inspections->save();
    }
}
