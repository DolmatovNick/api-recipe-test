<?php

namespace Recapi\Models;

use DB\UserAdapter;

class User {

    public $id;
    public $username;
    public $password;
    public $apikey;

    /**
     * Construct by id
     * @param $id
     */
    public static function getByID(int $id) : User
    {
        return UserAdapter::fillByID($id);
    }

    public function save()
    {
        return UserAdapter::save($this);
    }

    public function authenticate()
    {
        $user = UserAdapter::fillByApiKey($this);

        $this->id  = $user->id;
        $this->username  = $user->username;
        $this->password  = $user->password;
        $this->apikey  = $user->apikey;

        if ( $this->id == null ) {
            return false;
        }

        static::setCurrentUser($this);
        return true;
    }

    protected static function setCurrentUser(User $user)
    {
        $_SESSION['user'] = serialize($user);
    }

    public static function getCurrentUser() : User
    {
        return unserialize( $_SESSION['user'] );
    }

    public function output()
    {
        return [
            'username'  =>  $this->username,
            'user_url'  => 'users/' . $this->id,
            'key' => $this->apikey
        ];
    }

}