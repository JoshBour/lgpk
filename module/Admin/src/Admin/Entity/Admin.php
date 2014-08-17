<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 24/5/2014
 * Time: 9:35 μμ
 */

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Admin
 * @package Admin\Entity
 * @ORM\Entity
 * @ORM\Table(name="admins")
 */
class Admin {

    const HASH_SALT = 'inFohash34@1!/D';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=5, name="admin_id")
     */
    private $adminId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * Hash the password.
     *
     * @param string $password
     * @return string
     */
    public static function getHashedPassword($password)
    {
        return crypt($password . self::HASH_SALT);
    }

    /**
     * Check if the user's password is the same as the provided one.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public static function hashPassword($user, $password)
    {
        return ($user->getPassword() === crypt($password . self::HASH_SALT, $user->getPassword()));
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }


} 