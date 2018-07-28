<?php
/*
******************************************************************
Copyright (c) 2016, Thiago Medeiros de Menezes
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are not permitted without specific prior written
permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
******************************************************************
*/

class JSONVoucher extends JSON {

	public function add( ) {
		$params = $this->format_params( );
		array_shift($params);
		$this->json($this->control->model->add($params));
	}

	public function list_valid( ) {
		$recipient_id = (int) $_POST['params'];
		$vouchers = $this->control->model->find_valid_by_recipient($recipient_id);
		$this->json($vouchers);
	}

	public function validate( ) {
		$code = filter_var($_POST['params'][0], FILTER_SANITIZE_STRING);
		$email = filter_var($_POST['params'][1], FILTER_SANITIZE_EMAIL);
		$this->json($this->control->model->validate($code, $email));
	}

	private function format_params( ) {
		$i = 0;
		$params[':voucher_id'] = (int) $_POST['params'][$i++];
		$params[':offer_id'] = (int) $_POST['params'][$i++];
		$params[':recipient_id'] = (int) $_POST['params'][$i++];
		$params[':expiration_date'] = $_POST['params'][$i++];
		return $params;
	}

}
?>
