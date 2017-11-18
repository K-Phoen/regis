<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateSchema extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function change()
    {
        $inspections = $this->table('inspection', ['id' => false, 'primary_key' => ['id']]);
        $githubPrInspections = $this->table('github_pr_inspection', ['id' => false, 'primary_key' => ['id']]);
        $reports = $this->table('report', ['id' => false, 'primary_key' => ['id']]);
        $analyses = $this->table('analysis', ['id' => false, 'primary_key' => ['id']]);
        $violations = $this->table('violation', ['id' => false, 'primary_key' => ['id']]);
        $repositories = $this->table('repository', ['id' => false, 'primary_key' => ['id']]);
        $users = $this->table('user_account', ['id' => false, 'primary_key' => ['id']]);
        $teams = $this->table('team', ['id' => false, 'primary_key' => ['id']]);
        $usersTeams = $this->table('team_user', ['id' => false, 'primary_key' => ['user_id', 'team_id']]);
        $repositoriesTeams = $this->table('team_repository', ['id' => false, 'primary_key' => ['repository_id', 'team_id']]);
        $usersGithub = $this->table('user_github', ['id' => false, 'primary_key' => ['id']]);
        $usersBitbucket = $this->table('user_bitbucket', ['id' => false, 'primary_key' => ['id']]);
        $bitbucketPrInspections = $this->table('bitbucket_pr_inspection', ['id' => false, 'primary_key' => ['id']]);

        $users
            ->addColumn('id', 'uuid')
            ->addColumn('roles', 'text', ['default' => 'ROLE_USER'])
        ;

        $teams
            ->addColumn('id', 'uuid')
            ->addColumn('owner_id', 'uuid')
            ->addColumn('name', 'string')
            ->addForeignKey('owner_id', 'user_account', 'id')
        ;

        $usersTeams
            ->addColumn('team_id', 'uuid')
            ->addColumn('user_id', 'uuid')
            ->addForeignKey('team_id', 'team', 'id')
            ->addForeignKey('user_id', 'user_account', 'id')
        ;

        $repositoriesTeams
            ->addColumn('team_id', 'uuid')
            ->addColumn('repository_id', 'uuid')
            ->addForeignKey('team_id', 'team', 'id')
            ->addForeignKey('repository_id', 'repository', 'id')
        ;

        $usersGithub
            ->addColumn('id', 'uuid')
            ->addColumn('user_id', 'uuid')
            ->addColumn('remote_id', 'integer')
            ->addColumn('username', 'string')
            ->addColumn('access_token', 'string')
            ->addForeignKey('user_id', 'user_account', 'id')
        ;

        $usersBitbucket
            ->addColumn('id', 'uuid')
            ->addColumn('user_id', 'uuid')
            ->addColumn('remote_id', 'string')
            ->addColumn('username', 'string')
            ->addColumn('access_token', 'string')
            ->addForeignKey('user_id', 'user_account', 'id')
        ;

        $inspections
            ->addColumn('id', 'uuid')
            ->addColumn('repository_id', 'uuid')
            ->addColumn('report_id', 'uuid', ['null' => true])
            ->addColumn('created_at', 'datetime', ['timezone' => true])
            ->addColumn('started_at', 'datetime', ['timezone' => true, 'null' => true])
            ->addColumn('finished_at', 'datetime', ['timezone' => true, 'null' => true])
            ->addColumn('base', 'string')
            ->addColumn('head', 'string')
            ->addColumn('status', 'string')
            ->addColumn('type', 'string')
            ->addColumn('failure_trace', 'text')
            ->addForeignKey('report_id', 'report', 'id')
            ->addForeignKey('repository_id', 'repository', 'id')
        ;

        $repositories
            ->addColumn('id', 'uuid')
            ->addColumn('identifier', 'string')
            ->addColumn('type', 'string')
            ->addColumn('name', 'string')
            ->addColumn('owner_id', 'uuid')
            ->addColumn('shared_secret', 'text')
            ->addColumn('is_inspection_enabled', 'boolean')
            ->addForeignKey('owner_id', 'user_account', 'id')
            ->addIndex(['identifier', 'type'], [
                'unique' => true,
                'name' => 'idx_repo_id_unique',
            ])
        ;

        $githubPrInspections
            ->addColumn('id', 'uuid')
            ->addColumn('pull_request_number', 'integer')
            ->addForeignKey('id', 'inspection', 'id')
        ;

        $bitbucketPrInspections
            ->addColumn('id', 'uuid')
            ->addColumn('pull_request_number', 'integer')
            ->addForeignKey('id', 'inspection', 'id')
        ;

        $reports
            ->addColumn('id', 'uuid')
            ->addColumn('status', 'string')
            ->addColumn('errors_count', 'integer', ['default' => 0])
            ->addColumn('warnings_count', 'integer', ['default' => 0])
            ->addColumn('raw_diff', 'text')
        ;

        $analyses
            ->addColumn('id', 'uuid')
            ->addColumn('report_id', 'uuid')
            ->addColumn('type', 'text')
            ->addColumn('errors_count', 'integer', ['default' => 0])
            ->addColumn('warnings_count', 'integer', ['default' => 0])
            ->addForeignKey('report_id', 'report', 'id')
        ;

        $violations
            ->addColumn('id', 'uuid')
            ->addColumn('analysis_id', 'uuid')
            ->addColumn('severity', 'integer', ['limit' => \Phinx\Db\Adapter\PostgresAdapter::INT_SMALL])
            ->addColumn('file', 'string')
            ->addColumn('line', 'integer')
            ->addColumn('position', 'integer')
            ->addColumn('description', 'text')
            ->addForeignKey('analysis_id', 'analysis', 'id')
        ;

        $users->create();
        $usersGithub->create();
        $usersBitbucket->create();
        $repositories->create();
        $teams->create();
        $usersTeams->create();
        $repositoriesTeams->create();
        $reports->create();
        $inspections->create();
        $githubPrInspections->create();
        $bitbucketPrInspections->create();
        $analyses->create();
        $violations->create();
    }
}
