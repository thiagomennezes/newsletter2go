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

class HTMLSpecialOffer extends HTML {

	public function __construct(&$control) {
		parent::__construct($control);
	}

	public function retrieve_behavior( ) {
		header("Content-type: text/javascript");
		return '
			function app_offer( ) {
				app_initialize("#app-table-offers", "OFFER");
			}

			function app_offer_add_edit( ) {
				app_submit( );
			}
		';
	}

	public function retrieve_decoration( ) {
		return '
			#app-table-offers tr > td:nth-child(3) {
				text-align: right;
			}
		';
	}

	public function retrieve_default( ) {
		$term = filter_var($_POST['params'], FILTER_SANITIZE_STRING);
		$offers = empty($term)? $this->control->model->find_all( ) : $this->control->model->match($term);
		$content = '
			<div class="container-fluid app-container-custom">
				<div class="row">
					<div class="col-12"><h4>Special Offer<a href="'.URL.'" class="btn pull-right">&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a></h4><hr /></div>
					<div class="col-auto">
						<button type="button" class="btn btn-primary" appcmd="ADD">
							<i class="fa fa-plus" aria-hidden="true"></i> Add Special Offer
						</button>
					</div>
					<div class="col-auto">
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
							<input type="text" class="form-control" id="app-input-search" aria-describedby="basic-addon">
						</div>
					</div>
					<div class="col-12"><br /></div>
					'.$this->build_table($offers).'
				</div>
			</div>
			<script type="text/javascript">app_offer( );</script>
		';
		echo $content;
	}

	public function add( ) {
		echo $this->build_form('Add Special Offer', URL.'JSON/OFFER/ADD');
	}

	private function build_form($header, $action, $data = null) {
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
								<input type="text" class="form-control" name="params[]" maxlenght="64" />
							</div>
							<div class="form-group">
								<label>Discount</label>
								<input type="number" class="form-control" name="params[]" />
							</div>
							<button type="button" class="btn btn-primary app-confirm">Confirm</button>
						</form>
					</div>
					<div class="col"></div>
				</div>
			</div>
			<script type="text/javascript">app_offer_add_edit( );</script>
		';
	}

	private function build_table(&$data) {
		$trs = '';
		$size = count($data);
		for($i = 0; $i < $size; ++$i) {
			$trs .= '<tr>
				<td><input type="checkbox" class="form-control" value="'.$data[$i]['offer_id'].'"></td>
				<td>'.$data[$i]['offer'].'</td>
				<td>'.$data[$i]['discount'].'</td>
			</tr>';
		}
		return '
			<table class="table table-striped" id="app-table-offers">
				<thead>
					<tr>
						<th><input type="checkbox" class="form-control" id="app-checkbox-all"></th>
						<th>Special Offer</th>
						<th>Discount</th>
					</tr>
				</thead>
				<tbody>'.$trs.'</tbody>
			</table>
		';
	}

}
?>
