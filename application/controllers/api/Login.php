<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Login extends REST_Controller
{
    public function index_post()
    {
        $_POST = $this->security->xss_clean($_POST);


        #form validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == false) {
            $message = array(
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors()
            );

            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        } else {

            $output = $this->Login_model->userLogin($this->input->post('username'), $this->input->post('password'));
            if (!empty($output) and $output != FALSE) {
                $return_data = [
                    'username' => $output->username,
                    'nama' => $output->nama,
                    'jenis_kelamin' => $output->jenis_kelamin,
                    'tgl_lahir' => $output->tgl_lahir,
                    'alamat' => $output->alamat,
                    'role_id' => $output->role_id
                ];

                // Login Success
                $message = [
                    'status' => 'success',
                    'data' => $return_data,
                    'message' => "User login successful"
                ];
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                // Login Error
                $message = [
                    'status' => 'failed',
                    'message' => "Invalid Username or Password"
                ];
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
}
