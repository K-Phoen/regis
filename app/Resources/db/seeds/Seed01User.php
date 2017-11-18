<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class Seed01User extends AbstractSeed
{
    public function run()
    {
        $users = $this->table('user_account');
        $usersGithub = $this->table('user_github');

        $users->insert([
            [
                'id' => 'd67ff369-704b-4315-a75f-b67f5bc9cc5a',
                'roles' => 'ROLE_USER',
            ],
        ]);

        $usersGithub->insert([
            [
                'id' => 'bf323fb5-2661-4bc7-bc32-0385b05017be',
                'user_id' => 'd67ff369-704b-4315-a75f-b67f5bc9cc5a',
                'username' => 'K-Phoen',
                'remote_id' => 42,
                'access_token' => 'fake access token',
            ],
        ]);

        $users->save();
        $usersGithub->save();
    }
}
