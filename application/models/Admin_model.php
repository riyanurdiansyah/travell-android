<?php

class Admin_model extends CI_Model
{

    public function getUser()
    {
        $this->db->select('*');
        $this->db->from('tb_users');
        $this->db->where('role_id', 2);
        $this->db->order_by('nama', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getBooking()
    {
        $this->db->select('*');
        $this->db->from('tb_booking');
        $this->db->where('status_pembayaran', 2);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getBooked()
    {
        $this->db->select('*');
        $this->db->from('tb_booking');
        $this->db->where('status_pembayaran', 3);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function updateDataTanggal()
    {
        $query = $this->db->query("SELECT DAYOFMONTH(tanggal_berangkat) AS bulan FROM tb_booking");

        return $query->result_array();
    }

    public function getTahun()
    {
        $query = $this->db->query("SELECT YEAR(tanggal_berangkat) AS tahun FROM tb_booking GROUP BY YEAR(tanggal_berangkat) ORDER BY
        YEAR(tanggal_berangkat) ASC");

        return $query->result_array();
    }

    public function getDataByTanggal($newAwal, $newAkhir)
    {
        $query = $this->db->query("SELECT * FROM tb_booking WHERE status_pembayaran = 4 AND tanggal_berangkat BETWEEN '$newAwal' AND '$newAkhir' ORDER BY tanggal_berangkat ASC");

        return $query->result_array();
    }

    public function getDataByMonth($tahun, $newBulanAwal, $newBulanAkhir)
    {

        $query = $this->db->query("SELECT * FROM tb_booking WHERE YEAR(tanggal_berangkat) = '$tahun' AND MONTH(tanggal_berangkat) BETWEEN '$newBulanAwal' 
        AND '$newBulanAkhir' ORDER BY tanggal_berangkat ASC ");

        return $query->result_array();
    }
}
