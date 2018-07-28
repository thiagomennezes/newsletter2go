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

define('SHR', '/newsletter2go/share/');
define('URL', '/newsletter2go/');
define('LG_FAILURE_UNEXPECTED_ERROR', 'Unexpected error! Please contact your system administrator.');
define('LG_SUCCESS', 'Operation successfully completed!');

require_once('classes/model/Model.php');
require_once('classes/model/ModelRecipient.php');
require_once('classes/model/ModelSpecialOffer.php');
require_once('classes/model/ModelVoucher.php');
require_once('classes/view/View.php');
require_once('classes/view/json/JSON.php');
require_once('classes/view/json/JSONSpecialOffer.php');
require_once('classes/view/json/JSONRecipient.php');
require_once('classes/view/json/JSONVoucher.php');
require_once('classes/view/html/HTMLBuilder.php');
require_once('classes/view/html/HTML.php');
require_once('classes/view/html/HTMLSpecialOffer.php');
require_once('classes/view/html/HTMLRecipient.php');
require_once('classes/view/html/HTMLVoucher.php');

class Control {

	public $model = null;
	public $view = null;

	protected $command = null;
	protected $executor = null;

	public function __construct( ) {
		session_start( );
		$this->assign_command($command);
		$this->assign_executor( );
		$this->format_input($_GET['params']);
		$this->format_input($_POST['params']);
		$this->initialize( );
	}

	public function execute( ) {
		try {
			$this->model = new Model($this);
			$this->view['JSON'] = new JSON($this);
			$this->view['HTML'] = new HTML($this);
			switch(array_shift($this->command)) {
				case 'OFFER': $this->execute_offer( ); break;
				case 'RECIPIENT': $this->execute_recipient( ); break;
				case 'VOUCHER': $this->execute_voucher( ); break;
				case 'RETRIEVE_BEHAVIOR': $this->executor->retrieve_behavior( ); break;
				case 'RETRIEVE_DECORATION': $this->executor->retrieve_decoration( ); break;
				default: $this->executor->retrieve_default( ); break;
			}
		}
		catch(Exception $e) { $this->execute_exception($e); }
	}

	private function execute_recipient( ) {
		$this->initialize_mv('Recipient');
		switch(array_shift($this->command)) {
			case 'ADD': $this->executor->add( ); break;
			case 'MATCH': $this->executor->match( ); break;
			default: $this->executor->retrieve_default( ); break;
		}
	}

	private function execute_offer( ) {
		$this->initialize_mv('SpecialOffer');
		switch(array_shift($this->command)) {
			case 'ADD': $this->executor->add( ); break;
			case 'MATCH': $this->executor->match( ); break;
			default: $this->executor->retrieve_default( ); break;
		}
	}

	private function execute_voucher( ) {
		$this->initialize_mv('Voucher');
		switch(array_shift($this->command)) {
			case 'ADD': $this->executor->add( ); break;
			case 'EDIT': $this->executor->edit( ); break;
			case 'LIST': $this->executor->list_valid( ); break;
			case 'MATCH': $this->executor->match( ); break;
			case 'REMOVE': $this->executor->remove( ); break;
			case 'VALIDATE': $this->executor->validate( ); break;
			default: $this->executor->retrieve_default( ); break;
		}
	}

	private function execute_exception(&$exception) {
		$this->model->rollback( );
		$_SESSION['ExceptionMessage'] .= (!empty($_SESSION['ExceptionMessage']) && !empty($exception))? "\n".$exception->getMessage( ) : $exception->getMessage( );
		$this->view['JSON']->json(array('fail' => true, 'message' => $_SESSION['ExceptionMessage'], 'data' => $_SESSION['ExceptionData'], 'url' => $_SESSION['ExceptionURL']));
		error_log($_SESSION['ExceptionMessage']);
		if($_SESSION['ExceptionSignOut']) {
			session_unset( );
			session_destroy( );
			exit( );				
		}
	}

	protected function assign_command(&$command) {
		if(empty($command)) {
			$split = explode('?', $_SERVER['REQUEST_URI']);
			$this->command = array_slice(explode('/', reset($split)), 2);
		}
		else $this->command = explode('/', $command);
	}

	protected function assign_executor( ) {
		switch(array_shift($this->command)) {
			case 'JSON': $this->executor = &$this->view['JSON']; break;
			default: $this->executor = &$this->view['HTML']; break;
		}
	}

	private function format_input(&$input) {
		if(isset($input)) {
			if(is_array($input))
				$input = json_decode(str_replace('""', "null", json_encode($input)), true);
			else $input = empty($input)? null : $input;
		}
	}

	private function initialize( ) {
		$_SESSION['ExceptionMessage'] = null;
		$_SESSION['ExceptionData'] = null;
		$_SESSION['ExceptionURL'] = URL;
		$_SESSION['ExceptionSignOut'] = false;
		date_default_timezone_set($_SESSION['SystemTimezone']?? 'Europe/Berlin');
		ini_set('session.cache_expire', $_SESSION['SystemSessionTime']?? 3600);
		ini_set('session.cookie_httponly', true);
		ini_set('session.use_only_cookie', true);
	}

	private function initialize_mv($class) {
		$model = "Model$class";
		$json = "JSON$class";
		$html = "HTML$class";
		$this->model = new $model($this);
		$this->view['JSON'] = new $json($this);
		$this->view['HTML'] = new $html($this);
	}

}
?>
