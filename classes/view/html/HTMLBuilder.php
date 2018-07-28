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

trait HTMLBuilder {

	function build_html(&$content) {
		echo '
			<!DOCTYPE html />
			<html lang="de">
				<head>
					<title>Newsletter2go</title>
					<meta charset="utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1.0" />
					<meta name="author" content="Thiago Medeiros de Menezes" />
					<meta name="apple-mobile-web-app-capable" content="yes">
					<link rel="stylesheet" href="https://code.jquery.com/ui/jquery-ui-git.css">
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
					<link href="'.SHR.'font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
					<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
					<link href="'.URL.'HTML/RETRIEVE_DECORATION" rel="stylesheet" type="text/css" />
					<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
					<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
					<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
					<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
					<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
					<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
					<script type="text/javascript" src="'.URL.'HTML/RETRIEVE_BEHAVIOR"></script>
				</head>
				<body>
					<div class="d-none overlay">
						<div class="app-progressbar"></div>
						<img class="app-loader" src="'.SHR.'images/loader.gif" />
					</div>
					'.$content.'
				</body>
			</html>
		';
	}

	function build_copyright( ) {
		return "
			/*
			******************************************************************
			Copyright (c) 2017, Thiago Medeiros de Menezes
			All rights reserved.

			Redistribution and use in source and binary forms, with or without
			modification, are not permitted without specific prior written
			permission.

			THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
			'AS IS' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
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
		";
	}

	function build_options($data, $target, $key_id, $key_value) {
		$options = null;
		$target = (array) $target;
		$size_d = count($data);
		for($i = 0; $i < $size_d; ++$i) {
			$selected = null;
			$size_t = count($target);
			for($j = 0; $j < $size_t; ++$j) {
				if($data[$i][$key_id] == $target[$j]) {
					$selected = 'selected';
					array_splice($target, $j, 1);
					break;
				}
			}
			$options .= '<option value="'.$data[$i][$key_id].'" '.$selected.'>'.$data[$i][$key_value].'</option>';
		}
		return $options;
	}

}
?>
