<?php
/*
******************************************************************
Copyright (c) 2017, Thiago Medeiros de Menezes
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

class ModelVoucher extends Model {

	public function add(&$params) {
		$this->begin_transaction( );
		$sql = "INSERT INTO VoucherCode (offer_id, recipient_id, expiration_date) VALUES (:offer_id, :recipient_id, :expiration_date)";
		$this->query($sql, $params);
		$voucher_id = $this->get_last_inserted_id( );
		$sql = "UPDATE VoucherCode SET code = CRC32($voucher_id) WHERE voucher_id = $voucher_id";
		$this->query($sql);
		$this->commit( );
		return array(fail => false, 'message' => LG_SUCCESS, 'url' => URL);
	}

	public function count_vouchers( ) {
		$sql = "
			SELECT
				(SELECT COUNT(voucher_id) FROM VoucherCode) AS total,
				(SELECT COUNT(voucher_id) FROM VoucherCode WHERE used_date IS NULL) AS not_used,
				(SELECT COUNT(voucher_id) FROM VoucherCode WHERE used_date IS NOT NULL) AS used
		";
		$rs = $this->fetch_all($sql); 
		return $rs[0];
	}

	public function find_all($modifier = null) {
		$sql = "
			SELECT *, DATE_FORMAT(vc.used_date, '%d.%m.%Y') AS uf_date
			FROM VoucherCode AS vc
			INNER JOIN Recipient AS r ON r.recipient_id = vc.recipient_id
			INNER JOIN SpecialOffer AS so ON so.offer_id = vc.offer_id
			$modifier
		";
		return $this->fetch_all($sql);
	}

	public function find_valid_by_recipient(int &$recipient_id) {
		return $this->find_all("WHERE vc.recipient_id = $recipient_id AND vc.used_date IS NULL");
	}

	public function find_offers( ) {
		$model = new ModelSpecialOffer($this->control);
		return $model->find_all( );
	}

	public function match(string &$term) {
		return $this->find_all("WHERE vc.code LIKE '$term%' OR r.email LIKE '$term%' OR so.offer LIKE '$term%'");
	}

	public function validate(string &$code, string &$email) {
		$rs = $this->find_all("WHERE vc.code = '$code' AND r.email = '$email' AND vc.used_date IS NULL");
		if(empty($rs))
			throw new Exception("Ungültiger code oder e-mail!");
		$sql = "UPDATE VoucherCode SET used_date = NOW( ) WHERE voucher_id = {$rs[0]['voucher_id']}";
		$this->query($sql);
		return array('fail' => false, 'message' => LG_SUCCESS." Der Rabatt ist {$rs[0]['discount']}%.", 'url' => URL.'HTML/VOUCHER', 'data' => $rs[0]['discount']);
	}

}

?>

