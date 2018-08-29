@extends("packages.page")
@section("content")
<div class="panel panel-default">
  <div class="panel-heading"><strong>Packages to be {{ ($status == "PENDING")?'Approved':(($status == "AWAITING UPLOAD")?'Verified':(($status == "APPROVED")?'Reverted':'Deleted')) }}</strong></div>
  <div class="panel-body">
<?php
  if($status == "PENDING"){
		$actions = '<input type="submit" name="submit" value="Download" class="btn btn-info"> &nbsp; <input type="submit" name="submit" value="Approve" class="btn btn-success"> &nbsp; ';
	} elseif($status == "APPROVED"){
		$actions = '';
	} elseif($status == "AWAITING UPLOAD") {
		$actions = '<input type="submit" name="submit" value="Verify File" class="btn btn-primary">';
	} else {
		$actions = '<input type="submit" name="submit" value="Delete Package" class="btn btn-danger">';
	}
?>
@if(isset($data) && !empty($data))
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
					<tr><th width="5%">No</th><th width="10%">Product</th><th width="10%">Edition</th><th width="10%">Package</th><th width="35%">Version</th>{!! ($status == "DELETE")?'<th>Current Status</th>':'' !!}<th width="30%">Action</th></tr>
				</thead>
				<tbody>
				@foreach($data as $Iter => $VersionArray)
				<?php if(!$VersionArray['product']['code'] || !$VersionArray['edition']['code'] || !$VersionArray['package']['code']) continue; ?>
				<?php $frmId = "frm_".$VersionArray['product']['code'].$VersionArray['edition']['code'].$VersionArray['package']['code'].$VersionArray['version_sequence']; ?>
					<tr>
						<td align="center">{{ $Iter+1 }}</td>
						<td>{{ $VersionArray['product']['name'] }}</td>
						<td>{{ $VersionArray['edition']['name'] }}</td>
						<td>{{ $VersionArray['package']['name'] }}</td>
						<td>{{ $VersionArray['version_string'] . $VersionArray['version_numeric'] }}</td>
						{!! ($status == "DELETE")?'<td>'.$VersionArray['status'].'</td>':'' !!}
						<td align="center">
							<form method="post" id="{{ $frmId }}">
								{{ csrf_field() }}
								<input type="hidden" name="product" value="{{ $VersionArray['product']['code'] }}">
								<input type="hidden" name="edition" value="{{ $VersionArray['edition']['code'] }}">
								<input type="hidden" name="package" value="{{ $VersionArray['package']['code'] }}">
								<input type="hidden" name="sequence" value="{{ $VersionArray['version_sequence'] }}">
								{!! $actions !!}
								{!! ($status == "PENDING")?'<a href="javascript:RejectReason(\''.$frmId.'\')" class="btn btn-primary">Reject</a>':'' !!}
								{!! ($status == "PENDING" || $status == "APPROVED")?'<input type="hidden" name="reason">':'' !!}
								{!! ($status == "APPROVED")?'<a class="btn btn-primary" href="javascript:WithDrawReason(\''.$frmId.'\',\''.$VersionArray['product']['name'].'\',\''.$VersionArray['edition']['name'].'\',\''.$VersionArray['package']['name'].'\',\''.$VersionArray['version_numeric'].'\')">Revert Package</a>':'' !!}
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
@else
		<h3 class="text-center">No Records found</h3>
@endif
  </div>
</div>
<div id="withdrawModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Reason to Revert package</h4>
			</div>
			<div class="modal-body clearfix">
				<div class="table-responsive">
					<table class="table table-striped">
						<tbody>
							<tr class="product"><th>Product</th><td></td></tr>
							<tr class="edition"><th>Edition</th><td></td></tr>
							<tr class="package"><th>Package</th><td></td></tr>
							<tr class="version"><th>Version</th><td></td></tr>
							<tr class="reason"><th>Revert Reason</th><td>
								<div class="form-group form-horizontal clearfix">
									<textarea name="modal_reason" style="height: 100px" class="form-control"></textarea>
								</div>
							</td></tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onClick="ConfirmWithDraw()">Revert Package</button>
			</div>
		</form></div>
	</div>
</div>
@endsection