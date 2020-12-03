<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Snap extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$params = array('server_key' => 'SB-Mid-server-nQnglLrigNuzgAmbUN6vF9zm', 'production' => false);
		$this->load->library('midtrans');
		$this->midtrans->config($params);
		$this->load->helper('url');
	}

	public function index()
	{
		$this->load->view('checkout_snap');
	}

	public function token_post()
	{

		$harga = $this->input->post('total_pembayaran');
		$kode = $this->input->post('kode_booking');
		$nama = $this->input->post('nama');
		$email = $this->input->post('email');
		$no_hp = $this->input->post('no_hp');


		// $harga = $this->get('total_pembayaran');
		// $kode = $this->get('kode_booking');
		// $nama = $this->get('nama');
		// $email = $this->get('email');
		// $no_hp = $this->get('no_hp');

		// Required
		$transaction_details = array(
			'order_id' => rand(),
			'gross_amount' => $harga,
		);

		// Optional
		$item1_details = array(
			'id' => $kode,
			'price' => $harga,
			'quantity' => 1,
			'name' => $nama
		);

		// Optional
		$item_details = array($item1_details);

		// Optional
		$customer_details = array(
			'first_name'    => $nama,
			'last_name'     => "",
			'email'         => $email,
			'phone'         => $no_hp,
		);

		// Data yang akan dikirim untuk request redirect_url.
		$credit_card['secure'] = true;
		//ser save_card true to enable oneclick or 2click
		//$credit_card['save_card'] = true;

		$time = time();
		$custom_expiry = array(
			'start_time' => date("Y-m-d H:i:s O", $time),
			'unit' => 'minute',
			'duration'  => 2
		);

		$transaction_data = array(
			'transaction_details' => $transaction_details,
			'item_details'       => $item_details,
			'customer_details'   => $customer_details,
			'credit_card'        => $credit_card,
			'expiry'             => $custom_expiry
		);

		error_log(json_encode($transaction_data));
		$snapToken = $this->midtrans->getSnapToken($transaction_data);
		error_log($snapToken);
		echo $snapToken;

		$this->response([
			'data' => $customer_details,
			'token' => $snapToken,
			'tr' => $transaction_data
		]);
	}

	public function finish()
	{
		$result = json_decode($this->input->post('result_data'));
		echo 'RESULT <br><pre>';
		var_dump($result);
		echo '</pre>';
	}
}
