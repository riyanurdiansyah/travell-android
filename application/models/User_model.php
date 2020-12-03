<?php

class User_model extends CI_Model
{

    public function getUsers($id = null)
    {
        if ($id === null) {
            return $this->db->get('tb_users')->result_array();
        } else {
            return $this->db->get_where('tb_users', ['id' => $id])->result_array();
        }
    }

    public function deleteUsers($id)
    {
        $this->db->delete('tb_users', ['id' => $id]);
        return $this->db->affected_rows();
    }

    public function createUsers($data)
    {
        $this->db->insert('tb_users', $data);
        return $this->db->affected_rows();
    }

    public function updateUsers($data, $username)
    {
        $this->db->update('tb_users', $data, ['username' => $username]);
        return $this->db->affected_rows();
    }

    public function createBooking($data)
    {
        $this->db->insert('tb_booking', $data);
        return $this->db->affected_rows();
    }

    public function updateBooking($data, $kode)
    {
        $this->db->update('tb_booking', $data, ['kode_booking' => $kode]);
        return $this->db->affected_rows();
    }
}
