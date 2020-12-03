<?php

class Login_model extends CI_Model
{
    public function userLogin($username, $password)
    {
        $this->db->where('username', $username);
        $q = $this->db->get('tb_users');

        if ($q->num_rows()) {
            $user_pass = $q->row('password');
            if (password_verify($password, $user_pass)) {
                return $q->row();
            }
            return false;
        } else {
            return false;
        }
    }
}
