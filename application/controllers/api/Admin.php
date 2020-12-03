<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Admin extends REST_Controller
{
    public function user_get()
    {
        $user = $this->Admin_model->getUser();
        $this->response($user, 200);
    }

    public function booking_get()
    {
        $user = $this->Admin_model->getBooking();
        $this->response($user, 200);
    }

    public function booked_get()
    {
        $user = $this->Admin_model->getBooked();
        $this->response($user, 200);
    }

    public function konfirmasi_post()
    {
        $kode_booking = $this->input->post('kode_booking');

        $data = [
            'kode_booking' => $kode_booking,
            'status_pembayaran' => 3
        ];

        $this->db->where('kode_booking', $kode_booking);
        $this->db->update('tb_booking', $data);

        $this->_sendEmail();

        $message = [
            'status' => 'success',
            'message' => 'Data berhasil dikonfirmasi',
            'data' => $data
        ];
        $this->response($message, 200);
    }

    private function _sendEmail()
    {
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'androprojectriyan@gmail.com',
            'smtp_pass' => 'dzneverinc',
            'smtp_port' => 465,
            'maitype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];

        $this->load->library('email', $config);

        $this->email->from('noreply@travellapps.com', 'TRAVELL APPS');
        $this->email->to($this->input->post('email'));
        $this->email->subject('Konfirmasi Pembayaran');
        $this->email->message('Pembayaran untuk kode booking' . " " . $this->input->post('kode_booking') .  " " . 'sudah diterima');

        $this->email->send();
    }

    public function datatahun_get()
    {
        $user = $this->Admin_model->getTahun();
        $this->response($user, 200);
    }

    //report laporan

    public function tanggal_get()
    {
        $tglAwal = $this->get('tanggal_awal');
        $tglAkhir = $this->get('tanggal_akhir');

        //ubah format tanggal
        $resultAwal = explode('-', $tglAwal);
        $tahunAwal = $resultAwal[2];
        $bulanAwal = $resultAwal[1];
        $hariAwal = $resultAwal[0];

        $resultAkhir = explode('-', $tglAkhir);
        $tahunAkhir = $resultAkhir[2];
        $bulanAkhir = $resultAkhir[1];
        $hariAkhir = $resultAkhir[0];

        //format baru
        $newAkhir = $tahunAkhir . '-' . $bulanAkhir . '-' . $hariAkhir;
        $newAwal = $tahunAwal . '-' . $bulanAwal . '-' . $hariAwal;


        if ($tglAwal == '') {
            $this->response([
                'status' => 'success',
                'message' => 'Laporan dari' . ' ' . $tglAwal . ' ' . 's/d' . ' ' . $tglAkhir
            ], REST_Controller::HTTP_OK);
        } else if ($tglAkhir == '') {
            $this->response([
                'status' => 'success',
                'message' => 'Laporan dari' . ' ' . $tglAwal . ' ' . 's/d' . ' ' . $tglAkhir
            ], REST_Controller::HTTP_OK);
        } else {
            $users = $this->Admin_model->getDataByTanggal($newAwal, $newAkhir);
            if ($users) {
                $this->response([
                    'status' => 'success',
                    'message' => 'Laporan dari tanggal' . ' ' . $tglAwal . ' ' . 's/d' . ' ' . $tglAkhir,
                    'data' => $users
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => 'not found',
                    'message' => 'Laporan dari tanggal' . ' ' . $tglAwal . ' ' . 's/d' . ' ' . $tglAkhir . ' ' . 'tidak ditemukan'
                ], REST_Controller::HTTP_OK);
            }
        }
    }

    public function update_post()
    {
        $tanggal = $this->input->post("tanggal_pemesanan");

        $this->db->delete('tb_booking', ['tanggal_pemesanan' => $tanggal]);
    }
}
