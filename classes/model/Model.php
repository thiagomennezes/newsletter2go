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

class Model {

	public $control = null;
	public $pdo = null;

	public function __construct(&$control) {
		$this->control = $control;
		$this->pdo = new PDO(
			'mysql:host=localhost;dbname=NEWSLETTER2GO',
			'newsletter2go',
			'3u_f4l0_4lem@0',
			[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
		);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function begin_transaction( ) {
		$this->pdo->beginTransaction( );
	}

	public function commit( ) {
		if($this->pdo->inTransaction( ))
			$this->pdo->commit( );
	}

	public function fetch_all($sql, $params = null) {
		$stmts = $this->pdo->prepare($sql);
		$stmts->execute($params);
		return $stmts->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function get_last_inserted_id( ) {
		return $this->pdo->lastInsertId( );
	}

	public function rollback( ) {
		if($this->pdo->inTransaction( ))
			$this->pdo->rollback( );
	}

	public function query($sql, $params = null) {
		$offset = timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime( ));
		$offset = sprintf("%s%02d:%02d", ($offset >= 0) ? '+' : '-', abs($offset/3600), abs($offset%3600));
		$this->pdo->exec("SET time_zone = '$offset'");
		$stmts = $this->pdo->prepare($sql);
		$stmts->execute($params);
	}

}

?>

