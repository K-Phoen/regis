<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateInspectionTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $inspections = $this->table('inspection', ['id' => false, 'primary_key' => ['id']]);
        $githubPrInspections = $this->table('github_pr_inspection', ['id' => false, 'primary_key' => ['id']]);
        $reports = $this->table('report', ['id' => false, 'primary_key' => ['id']]);
        $analyses = $this->table('analysis', ['id' => false, 'primary_key' => ['id']]);
        $violations = $this->table('violation', ['id' => false, 'primary_key' => ['id']]);

        $inspections
            ->addColumn('id', 'uuid')
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
        ;

        $githubPrInspections
            ->addColumn('id', 'uuid')
            ->addColumn('pull_request_number', 'integer')
            ->addColumn('repository_id', 'string')
            ->addForeignKey('id', 'inspection', 'id')
            //->addForeignKey('repository_id', 'repository', 'identifier')
        ;

        $reports
            ->addColumn('id', 'uuid')
            ->addColumn('status', 'string')
            ->addColumn('raw_diff', 'text')
        ;

        $analyses
            ->addColumn('id', 'uuid')
            ->addColumn('report_id', 'uuid')
            ->addColumn('type', 'text')
            ->addForeignKey('report_id', 'report', 'id')
        ;

        $violations
            ->addColumn('id', 'uuid')
            ->addColumn('analysis_id', 'uuid')
            ->addColumn('severity', 'integer', ['limit' => \Phinx\Db\Adapter\PostgresAdapter::INT_SMALL])
            ->addColumn('file', 'string')
            ->addColumn('line', 'integer')
            ->addColumn('position', 'integer')
            ->addColumn('description', 'string')
            ->addForeignKey('analysis_id', 'analysis', 'id')
        ;

        $reports->create();
        $inspections->create();
        $githubPrInspections->create();
        $analyses->create();
        $violations->create();
    }
}
