<?php


use Phinx\Migration\AbstractMigration;

class ChangeBitbucketRemoteIdToString extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function change()
    {
        $usersBitbucket = $this->table('user_bitbucket', ['id' => false, 'primary_key' => ['id']]);

        $usersBitbucket->changeColumn('remote_id', 'string');

        $usersBitbucket->save();
    }
}
