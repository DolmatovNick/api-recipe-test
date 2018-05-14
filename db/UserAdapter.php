<?php


namespace DB;

use PDO;
use Recapi\Environment;
use Recapi\Models\User;

class UserAdapter {

    public static function save(User $user)
    {
        /**
         * @var $db PDO
         */
        $db = Environment::get('db');
        $sql = 'INSERT INTO users (username, password, apikey )
            VALUES (:username, :password, :apikey )';

        /**
         * @var $sth \PDOStatement
         */
        $sth = $db->prepare($sql);
        $sth->bindValue(':username', $user->username, PDO::PARAM_STR);
        $sth->bindValue(':password', $user->password, PDO::PARAM_STR);
        $sth->bindValue(':apikey', $user->apikey, PDO::PARAM_STR);

        if ($sth->execute()) {
            $user->id = $db->lastInsertId();
            return true;
        }

        return false;
    }

    public static function fillByApiKey(User &$user) : User {
        /**
         * @var $db PDO
         * @var $sth \PDOStatement
         */
        $db = Environment::get('db');

        $sth = $db->prepare("SELECT id, username, password, apikey FROM users WHERE apikey = :apikey");
        $sth->bindValue(':apikey', $user->apikey, PDO::PARAM_STR);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_CLASS, User::class);

        if (count($res) == 1) {
            return $res[0];
        }

        return new User();
    }


}