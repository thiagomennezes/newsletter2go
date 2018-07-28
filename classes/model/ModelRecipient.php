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

class ModelRecipient extends Model {

	public function add(&$params) {
		$sql = "INSERT INTO Recipient (recipient, email) VALUES (:recipient, :email)";
		$this->query($sql, $params);
		return array(fail => false, 'message' => LG_SUCCESS, 'url' => URL.'HTML/RECIPIENT');
	}

	public function find_all($modifier = null) {
		$sql = "SELECT * FROM Recipient $modifier ORDER BY recipient, email";
		return $this->fetch_all($sql);
	}

	public function match(string &$term) {
		return $this->find_all("WHERE recipient LIKE '%$term%' OR email LIKE '%$term%'");
	}

}

?>

