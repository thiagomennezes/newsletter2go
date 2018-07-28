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

class HTMLVoucher extends HTML {

	public function __construct(&$control) {
		parent::__construct($control);
	}

	public function retrieve_behavior( ) {
		header("Content-type: text/javascript");
		return '
			function app_voucher( ) {
				app_initialize("#app-table-vouchers", "VOUCHER");
			}

			function app_voucher_add_edit( ) {
				$("#app-input-recipient").autocomplete({
					source: function(request, response) { $.getJSON("'.URL.'JSON/RECIPIENT/MATCH", { params: request.term }, response ); },
					focus: function( ) { return false; },
					select: function(event, ui) {
						$("#app-input-recipient").val(ui.item.value);
						$("#app-input-recipient-id").val(ui.item.recipient_id);
						return false;
					}
				});
				app_submit( );
			}

			function app_voucher_validate( ) {
				app_submit( );
			}
		';
	}

	public function retrieve_decoration( ) {
		return '
			#app-table-vouchers tr > td:nth-child(1) {
				width: 5%;
			}

			#app-table-vouchers tr > td:nth-child(2) {
				width: 30%;
			}

			#app-table-vouchers tr > td:nth-child(3) {
				text-align: center;
			}

			#app-table-vouchers tr > td:nth-child(6) {
				text-align: right;
			}
		';
	}

	public function retrieve_default( ) {
		$term = filter_var($_POST['params'], FILTER_SANITIZE_STRING);
		$counters = $this->control->model->count_vouchers( );
		$vouchers = empty($term)? $this->control->model->find_all( ) : $this->control->model->match($term);
		$content = '
			<div class="container-fluid app-container-custom">
				<div class="row">
					<div class="col-12"><h4>Welcome Vouchers<a href="'.URL.'" class="btn pull-right">&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a></h4><hr /></div>
					<div class="col-4 text-center"><h4><strong>'.$counters['total'].'</strong><br />Total</h4></div>
					<div class="col-4 text-center"><h4><strong>'.$counters['not_used'].'</strong><br />Not Used</h4></div>
					<div class="col-4 text-center"><h4><strong>'.$counters['used'].'</strong><br />Used</h4></div>
					<div class="col-12"><br /></div>
				</div>
				<div class="row">
					<div class="col-auto">
						<button type="button" class="btn btn-primary" appcmd="ADD">
							<i class="fa fa-plus" aria-hidden="true"></i> Add Voucher
						</button>
					</div>
					<div class="col-auto">
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
							<input type="text" class="form-control" id="app-input-search" aria-describedby="basic-addon">
						</div>
					</div>
					<div class="col-12"><br /></div>
					'.$this->build_table($vouchers).'
				</div>
			</div>
			<script type="text/javascript">app_voucher( );</script>
		';
		echo $content;
	}

	public function add( ) {
		echo $this->build_form('Add Voucher', URL.'JSON/VOUCHER/ADD');
	}

	public function list_valid( ) {
		$recipient_id = (int) $_POST['params'];
		$vouchers = $this->control->model->find_valid_by_recipient($recipient_id);
		$content = '
			<div class="container-fluid app-container-custom">
				<div class="row">
					<div class="col-12"><h4>Recipient Voucher<a href="'.URL.'" class="btn pull-right">&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a></h4></div>
					'.$this->build_table($vouchers).'
				</div>
			</div>
			<script type="text/javascript">app_voucher( );</script>
		';
		echo $content;
	}

	public function validate( ) {
		$content = '
			<div class="container">
				<div class="row">
					<div class="col text-center"><legend>Validation</legend></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col" style="background-color: #ffffff; border-radius: 5px; padding-top: 15px;">
						<form method="post" action="'.URL.'JSON/VOUCHER/VALIDATE">
							<div class="form-group">
								<label>Code</label>
								<input type="text" class="form-control" name="params[]" maxlenght="8" />
							</div>
							<div class="form-group">
								<label>E-mail</label>
								<input type="email" class="form-control" name="params[]" />
							</div>
							<button type="button" class="btn btn-primary app-confirm">Confirm</button>
						</form>
					</div>
					<div class="col"></div>
				</div>
			</div>
			<script type="text/javascript">app_voucher_validate( );</script>
		';
		echo $content;
	}

	private function build_form($header, $action, $data = null) {
		$o_opts = $this->build_options($this->control->model->find_offers( ), null, 'offer_id', 'offer');
		return '
			<div class="container">
				<div class="row">
					<div class="col text-center"><legend>'.$header.'</legend></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col" style="background-color: #ffffff; border-radius: 5px; padding-top: 15px;">
						<form method="post" action="'.$action.'">
							<input type="hidden" name="params[]" />
							<div class="form-group">
								<label>Special Offer</label>
								<select class="form-control" name="params[]">'.$o_opts.'</select>
							</div>
							<div class="form-group">
								<label>Recipient</label>
								<input type="text" class="form-control" id="app-input-recipient" />
								<input type="hidden" name="params[]" id="app-input-recipient-id" />
							</div>
							<div class="form-group">
								<label>Code</label>
								<input type="text" class="form-control" readonly />
							</div>
							<div class="form-group">
								<label>Expiration Date</label>
								<input type="date" class="form-control" name="params[]" />
							</div>
							<button type="button" class="btn btn-primary app-confirm">Confirm</button>
						</form>
					</div>
					<div class="col"></div>
				</div>
			</div>
			<script type="text/javascript">app_voucher_add_edit( );</script>
		';
	}

	private function build_table(&$data) {
		$trs = '';
		$size = count($data);
		for($i = 0; $i < $size; ++$i) {
			$is_used = !empty($data[$i]['uf_date']);
			$icon_used = $is_used? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-remove" aria-hidden="true"></i>';
			$btn_apply = $is_used? '' : '<button type="button" class="btn btn-sm btn-primary pull-right" appcmd="VALIDATE" title="Validation"><i class="fa fa-legal" aria-hidden="true"></i></button>';
			$trs .= '<tr>
				<td><input type="checkbox" class="form-control" value="'.$data[$i]['voucher_id'].'"></td>
				<td><span class="app-code">'.$data[$i]['code'].'</span>'.$btn_apply.'</td>
				<td>'.$icon_used.'</td>
				<td>'.$data[$i]['offer'].'</td>
				<td class="text-primary app-email">'.$data[$i]['email'].'</td>
				<td>'.$data[$i]['uf_date'].'</td>
			</tr>';
		}
		return '
			<table class="table table-striped" id="app-table-vouchers">
				<thead>
					<tr>
						<th><input type="checkbox" class="form-control" id="app-checkbox-all"></th>
						<th>CODE</th>
						<th>USED</th>
						<th>SPECIAL OFFER</th>
						<th>RECIPIENT</th>
						<th>USED DATE</th>
					</tr>
				</thead>
				<tbody>'.$trs.'</tbody>
			</table>
		';
	}

}
?>
