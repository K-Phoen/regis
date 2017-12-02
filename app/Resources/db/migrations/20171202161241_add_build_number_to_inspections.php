<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddBuildNumberToInspections extends AbstractMigration
{
    public function up()
    {
        $inspectionsTable = $this->table('inspection', ['id' => false, 'primary_key' => ['id']]);
        $inspectionsTable->addColumn('build_number', 'integer', ['default' => 1]);
        $inspectionsTable->save();

        $inspections = $this->fetchAll('SELECT id, repository_id FROM inspection ORDER BY created_at ASC');

        $buildNumbersMap = [];
        foreach ($inspections as $inspection) {
            $buildNumbersMap[$inspection['repository_id']]++;
            $buildNumber = $buildNumbersMap[$inspection['repository_id']];

            $this->execute(sprintf("UPDATE inspection SET build_number = %d WHERE id = '%s'", $buildNumber, $inspection['id']));
        }

        $inspectionsTable
            ->addIndex(['repository_id', 'build_number'], [
                'unique' => true,
                'name' => 'idx_repository_build_number'
            ])
            ->save();
    }

    public function down()
    {
        $inspections = $this->table('inspection', ['id' => false, 'primary_key' => ['id']]);
        $inspections->removeColumn('build_number');
        $inspections->save();
    }
}
