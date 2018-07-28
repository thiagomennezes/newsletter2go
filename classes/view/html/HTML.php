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

class HTML extends View {

	use HTMLBuilder;

	public function __construct(&$control) {
		parent::__construct($control);
	}

	public function retrieve_behavior( ) {
		header("Content-type: text/javascript");
		$behavior = '
			function app_default( ) {
				H_WIN = $(window).height( );
				$(".row").first( ).css("margin-top", (100*(H_WIN/2 - $(".row").first( ).height( )/2)/H_WIN) + "vh");
				$(".btn").click(function( ) {
					switch($(this).attr("appcmd")) {
						case "OFFER": $("#app-content").load("'.URL.'HTML/OFFER"); break;
						case "RECIPIENT": $("#app-content").load("'.URL.'HTML/RECIPIENT"); break;
						case "VALIDATION": $("#app-content").load("'.URL.'HTML/VOUCHER/VALIDATE"); break;
						case "VOUCHER": $("#app-content").load("'.URL.'HTML/VOUCHER"); break;
					}
				});
			}

			function app_initialize(table, entity) {
				$(table).DataTable({
					"bLengthChange": false,
					"columnDefs": [{ "targets": 0, "orderable": false }],
					"info": false,
					"order": [[ 1, "asc" ]],
					"pageLength": 5,
					"searching": false
				});

				$("#app-checkbox-all").click(function( ) {
					$(table + " input[type=checkbox]").prop("checked", $(this).is(":checked"));
				});

				$(".btn").click(function( ) {
					switch($(this).attr("appcmd")) {
						case "ADD": $("#app-content").load("'.URL.'HTML/" + entity + "/ADD"); break;
						case "LIST": $("#app-content").load("'.URL.'HTML/VOUCHER/LIST", { params: $(table + " input:checked").val( ) }); break;
						case "VALIDATE":
							var tr = $(this).parents("tr").first( );
							tr.find("input[type=checkbox]").prop("checked", true);
							var code = tr.find(".app-code").text( );
							var email = tr.find(".app-email").text( );
							$.ajax({
								url: "'.URL.'JSON/VOUCHER/VALIDATE",
								type: "post",
								data: { params: [code, email] },
								dataType: "json",
								success: function(result) {
									app_unlock_screen( );
									alert(result.message);
									if(!result.fail)
										$("#app-content").load(result.url);
								},
								error: function(result) {
									app_unlock_screen( );
									alert("'.LG_FAILURE_UNEXPECTED_ERROR.'");
								}
							});
							break;
					}
				});

				$("#app-input-search").keyup(function(e) {
					if(e.keyCode == 13) {
						$("#app-content").load("'.URL.'HTML/" + entity, { params: $(this).val( ) });
					}
				});
			}

			function app_lock_screen(show_progressbar) {
				$(".overlay").removeClass("d-none");
				$(".overlay .app-progressbar").css("width", "0");
				if(show_progressbar === true)
					$(".overlay .app-progressbar").removeClass("hide");
				else $(".overlay .app-progressbar").addClass("hide");
			}

			function app_submit( ) {
				$(".app-confirm").click(function( ) {
					app_lock_screen( );
					var form = $(this).parents("form").first( );
					$.ajax({
						url: form.attr("action"),
						type: form.attr("method"),
						data: form.serializeArray( ),
						dataType: "json",
						success: function(result) {
							app_unlock_screen( );
							alert(result.message);
							if(!result.fail)
								$("#app-content").load(result.url);
						},
						error: function(result) {
							app_unlock_screen( );
							alert("'.LG_FAILURE_UNEXPECTED_ERROR.'");
						}
					});
				});
			}

			function app_unlock_screen( ) {
				$(".overlay").addClass("d-none");
				$(".overlay .app-progressbar").css("width", "0");
			}
		';
		$behavior .= $this->retrieve_children_functions('HTML', 'retrieve_behavior');
		echo $behavior;
	}

	public function retrieve_decoration( ) {
		$decoration = '
			body {
				background-color: #e8e8e8;
				padding: 15px;
			}

			.app-container-custom > .row {
				background-color: #ffffff;
				border-radius: 5px;
				margin-bottom: 15px;
				padding-top: 15px;
			}
		';
		$decoration .= $this->retrieve_children_functions('HTML', 'retrieve_decoration');
		echo $decoration;
	}

	public function retrieve_default( ) {
		$content = '
			<div id="app-content">
				<div class="container-fluid">
					<div class="row">
						<div class="col">
							<button class="btn col-12" appcmd="OFFER"><i class="fa fa-gift fa-5x" aria-hidden="true"></i><h4>SPECIAL OFFERS</h4></button>
						</div>
						<div class="col">
							<button class="btn col-12" appcmd="RECIPIENT"><i class="fa fa-user fa-5x" aria-hidden="true"></i><h4>RECIPIENTS</h4></button>
						</div>
						<div class="col">
							<button class="btn col-12" appcmd="VOUCHER"><i class="fa fa-ticket fa-5x" aria-hidden="true"></i><h4>VOUCHERS</h4></button>
						</div>
						<div class="col">
							<button class="btn col-12" appcmd="VALIDATION"><i class="fa fa-legal fa-5x" aria-hidden="true"></i><h4>VALIDATION</h4></button>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript"> app_default( ); </script>
		';
		$this->build_html($content);
	}

	protected function retrieve_children_functions($parent, $function) {
		$content = '';
		foreach(get_declared_classes( ) as $class)
			if(is_subclass_of($class, $parent))
				$content .= $class::$function( );
		return $content;
	}

}
?>
