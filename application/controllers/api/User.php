<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class User extends REST_Controller
{
    public function index_get()
    {
        $id = $this->get('id');
        if ($id === null) {
            $users = $this->User_model->getUsers();
        } else {
            $users = $this->User_model->getUsers($id);
        }

        if ($users) {
            $this->response([
                'status' => true,
                'data' => $users
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => "id tidak ditemukan"
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_delete()
    {
        $id = $this->delete('id');


        if ($id === null) {
            $this->response([
                'status' => true,
                'message' => "Masukkan id"
            ], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            if ($this->User_model->deleteUsers($id) > 0) {
                $this->response([
                    'status' => true,
                    'id' => $id,
                    'message' => 'deleted.',
                ], REST_Controller::HTTP_NO_CONTENT);
            } else {
                $this->response([
                    'status' => true,
                    'message' => "id tidak ditemukan"
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    public function register_post()
    {
        $this->form_validation->set_rules('email', 'Email', 'valid_email');

        if ($this->form_validation->run() == false) {
            $this->response([
                'status' => 'wrong',
                'message' => 'Silahkan menggunakan email yang valid'
            ], REST_Controller::HTTP_OK);
        } else {

            $username = $this->input->post('username');
            $email = $this->input->post('email');


            $data = [
                'username' => $this->post('username'),
                'email' => $this->post('email'),
                'nama' => $this->post('nama'),
                'password' => password_hash($this->post('password'), PASSWORD_DEFAULT),
                'no_hp' => $this->post('no_hp'),
                'jenis_kelamin' => $this->post('jenis_kelamin'),
                'tgl_lahir' => $this->post('tgl_lahir'),
                'alamat' => $this->post('alamat'),
                'role_id' => $this->post('role_id')
            ];

            $user = $this->db->get_where('tb_users', ['username' => $username])->row_array();
            $email = $this->db->get_where('tb_users', ['email' => $email])->row_array();

            if ($user) {
                $this->response([
                    'status' => 'failed',
                    'message' => 'Username sudah terdaftar'
                ], REST_Controller::HTTP_OK);
            } else {
                if ($email) {
                    $this->response([
                        'status' => 'error',
                        'message' => 'Email sudah digunakan'
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->User_model->createUsers($data);
                    $this->response([
                        'status' => 'success',
                        'message' => 'Registrasi berhasil',
                        'data' => $data
                    ], REST_Controller::HTTP_OK);
                }
            }
        }
    }

    public function booking_post()
    {
        $tanggal = $this->input->post('tanggal_berangkat');

        $result = explode('-', $tanggal);
        $tahun = $result[2];
        $bulan = $result[1];
        $hari = $result[0];

        $new = $tahun . '-' . $bulan . '-' . $hari;

        $data = [
            'kode_booking' => $this->post('kode_booking'),
            'email' => $this->post('email'),
            'nama' => $this->post('nama'),
            'no_hp' => $this->post('no_hp'),
            'jumlah_rombongan' => $this->post('jumlah_rombongan'),
            'waktu_travel' => $this->post('waktu_travel'),
            'tujuan' => $this->post('tujuan'),
            'tanggal_berangkat' => $new,
            'total_pembayaran' => $this->post('total_pembayaran'),
            'bukti_transfer' => "",
            'status_pembayaran' => 1,
            'tanggal_pemesanan' => $this->post('tanggal_pemesanan')
        ];
        $this->session->set_userdata($data);

        if ($this->User_model->createBooking($data) > 0) {
            $this->response([
                'status' => 'success',
                'message' => 'Pemesanan berhasil',
                'data' => $data
            ], 200);
        } else {
            $this->response([
                'status' => 'fail',
                'message' => 'Failed'
            ], 502);
        }
    }

    public function index_put()
    {
        $username = $this->put('username');

        $data = [
            'username' => $this->put('username'),
            'email' => $this->put('email'),
            'nama' => $this->put('nama'),
            'no_hp' => $this->put('no_hp'),
            'jenis_kelamin' => $this->put('jenis_kelamin'),
            'tgl_lahir' => $this->put('tgl_lahir'),
            'alamat' => $this->put('alamat')
        ];

        if ($this->User_model->updateUsers($data, $username) > 0) {
            $this->response([
                'status' => true,
                'message' => 'user has been updated'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => true,
                'message' => 'Failed'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function login_post()
    {
        $_POST = $this->security->xss_clean($_POST);

        #form validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == false) {
            $data = [
                'id' => '',
                'username' => '',
                'email' => '',
                'nama' => '',
                'no_hp' => '',
                'jenis_kelamin' => '',
                'tgl_lahir' => '',
                'alamat' => '',
                'role_id' => ''
            ];
            $message = array(
                'status' => 'error',
                'message' => 'Kolom username dan password tidak boleh kosong',
                'data' => $data
            );

            $this->response($message, REST_Controller::HTTP_OK);
        } else {

            $output = $this->Login_model->userLogin($this->input->post('username'), $this->input->post('password'));
            if (!empty($output) and $output != FALSE) {
                $data = [
                    'id' => $output->id,
                    'username' => $output->username,
                    'email' => $output->email,
                    'nama' => $output->nama,
                    'no_hp' => $output->no_hp,
                    'jenis_kelamin' => $output->jenis_kelamin,
                    'tgl_lahir' => $output->tgl_lahir,
                    'alamat' => $output->alamat,
                    'role_id' => $output->role_id
                ];

                // Login Success
                $message = [
                    'status' => 'success',
                    'message' => 'Login berhasil',
                    'data' => $data
                ];
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // Login Error

                $data = [
                    'id' => '',
                    'username' => '',
                    'email' => '',
                    'nama' => '',
                    'no_hp' => '',
                    'jenis_kelamin' => '',
                    'tgl_lahir' => '',
                    'alamat' => '',
                    'role_id' => ''
                ];
                $message = [
                    'status' => 'failed',
                    'message' => 'Username atau password salah',
                    'data' => $data
                ];
                $this->response($message, REST_Controller::HTTP_OK);
            }
        }
    }

    public function histori_get()
    {
        $email = $this->get('email');
        if ($email == '') {
            $histori = $this->db->get('tb_booking')->result();
        } else {
            $this->db->where('email', $email);
            $this->db->join('tb_status_pembayaran', 'tb_booking.status_pembayaran = tb_status_pembayaran.id');
            $this->db->order_by('status', 'ASC');
            $histori = $this->db->get('tb_booking')->result();
        }
        $this->response($histori, 200);
    }

    public function tes_put()
    {
        $kode = $this->put('kode_booking');
        $file = $this->request->getFile('bukti_transfer');

        $config['allowed_types']    = 'jpeg|jpg|png';
        $config['max_size']         = '0';
        $config['upload_path']      = './assets/Bukti Transfer';
        $config['file_name']        = $kode;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('bukti_transfer')) {
            $newFile = $this->upload->data('file_name');
            $data = [
                'kode_booking' => $this->put('kode_booking'),
                'bukti_transfer' => $newFile,
                'status_pembayaran' => $this->put('status_pembayaran')
            ];
            $this->User_model->updateBooking($data, $kode);
            $this->response([
                'status' => true,
                'message' => 'user has been updated'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => "Gagal"
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = [
            'kode_booking' => $this->put('kode_booking'),
            'status_pembayaran' => $this->put('status_pembayaran')
        ];

        if ($this->User_model->updateBooking($data, $kode) > 0) {
            $this->response([
                'status' => true,
                'message' => 'Data berhasil diupdate'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data gagal diupdate'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }


    public function update_post()
    {

        $kode_booking = $this->post('kode_booking');

        $config['upload_path']      = './assets/Bukti Transfer/';
        $config['allowed_types']    = 'png|jpg|jpeg';
        $config['max_size']         = '20480';
        $config['file_name']        = $kode_booking;
        $path = "./assets/Bukti Transfer/";
        $image = $_FILES['bukti_transfer']['name'];
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('bukti_transfer')) {
            $this->response(array('status' => 'fail', 502));
        } else {
            $newFile = $this->upload->data('file_name');
            $data = [
                'kode_booking' => $this->post('kode_booking'),
                'bukti_transfer' => $newFile,
                'status_pembayaran' => $this->post('status_pembayaran')
            ];
            $this->db->where('kode_booking', $kode_booking);
            $this->db->update('tb_booking', $data);

            $message = [
                'status' => 'success',
                'message' => 'Data berhasil diupdate',
                'data' => $data
            ];
            $this->response($message, 200);
        }
    }

    public function print_post()
    {
        $this->load->library('dompdf_gen');
        $kode = $this->input->post('kode_booking');

        $data = [
            'kode_booking' => $this->post('kode_booking'),
            'email' => $this->post('email'),
            'nama' => $this->post('nama'),
            'no_hp' => $this->post('no_hp'),
            'jumlah_rombongan' => $this->post('jumlah_rombongan'),
            'waktu_travel' => $this->post('waktu_travel'),
            'tujuan' => $this->post('tujuan'),
            'tanggal_berangkat' => $this->post('tanggal_berangkat'),
            'status_pembayaran' => $this->post('status_pembayaran')
        ];

        $this->load->view('bukti_pdf', $data);

        $paper_size = 'A4';
        $orientation = 'potrait';
        $html = $this->output->get_output();

        $this->dompdf->set_paper($paper_size, $orientation);

        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream("tes.pdf", array('Attachment' => 0));
        $message = [
            'status' => 'success',
            'message' => 'Bukti berhasil diprint',
            'data' => $data
        ];
        $this->response($message, 200);
    }

    public function pdf_post()
    {

        $kode = $this->input->post('kode_booking');

        $html = '<html>
        <head></head>
        <body>
            <h1>HELLO WORLD!</h1>
        </body>
        </html>
        ';

        //$pdf_filename  = 'report.pdf';
        $this->load->library('dompdf_lib');
        $this->dompdf_lib->convert_html_to_pdf($html, $kode . '.pdf', true);
    }
}
