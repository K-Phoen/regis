<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddUsernameToBitbucketUser extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function change()
    {
        $usersBitbucket = $this->table('user_bitbucket', ['id' => false, 'primary_key' => ['id']]);

        $usersBitbucket->addColumn('username', 'string');

        $usersBitbucket->save();
    }
}
