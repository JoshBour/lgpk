<?php
namespace Admin\Model;

use Zend\Authentication\Storage\Session;

class AuthStorage extends Session
{

    /**
     * Sets the remember me time for the logged admin.
     *
     * @param int $rememberMe
     * @param int $time
     */
    public function setRememberMe($rememberMe = 1, $time = 2419200)
    {
        if ($rememberMe == 1) $this->session->getManager()->rememberMe($time);
    }

    /**
     *  Resets the remember me time.
     */
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}
