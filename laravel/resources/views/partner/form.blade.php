@extends("partner.page")
@include('BladeFunctions')
@section("content")

<div class="content form">
<form action="{{ Route('partner.store') }}" method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading clearfix"><strong>New Partner</strong><a href="{{ Route('partner.index') }}" title="Back" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
		</div>
		<div class="panel-body clearfix">
			<div class="row">
				<div class="col col-md-6">
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3">Role</label>
						<div class="col-xs-9"><select required class="form-control" name="role">
							@foreach(\App\Models\Role::whereStatus('ACTIVE')->pluck('displayname','code') as $code => $name)
							<option value="{{ $code }}">{{ $name }}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3">Code</label>
						<div class="col-xs-9"><input type="text" value="" required class="form-control" name="code"></div>
					</div>
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3">Name</label>
						<div class="col-xs-9"><input type="text" value="" required class="form-control" name="name"></div>
					</div>
				</div>
				<div class="col col-md-6">
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3">Country</label>
						<div class="col-xs-9"><select required class="form-control" name="country" onChange="CountryChanged(this.value)">
							@foreach(\App\Models\Country::select('id','name','currency','phonecode')->get() as $C)
							<option value="{{ $C->id }}" data-currency="{{ $C->currency }}" data-phonecode="{{ $C->phonecode }}">{{ $C->name }}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3">Number</label>
						<div class="col-xs-9">
							<div class="input-group"><span class="input-group-addon phonecode"></span><input type="text" value="" class="form-control" name="phone"></div>
						</div>
					</div>
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3">Email</label>
						<div class="col-xs-9"><input type="text" value="" required class="form-control" name="email"></div>
						<input type="hidden" name="phonecode" value=""><input type="hidden" name="currency" value="">
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix"><input type="submit" value="Submit" class="pull-right btn btn-info" name="submit">
		</div>
	</div>
</form>
</div>


@endsection
@push('js')
<script type="text/javascript">
	function CountryChanged(C){
		OPT = $('[name="country"] option[value="'+C+'"]');
		CUR = OPT.attr('data-currency'); $('[name="currency"]').val(CUR)
		PHC = OPT.attr('data-phonecode'); $('[name="phonecode"]').val(PHC); $('.input-group-addon.phonecode').text(PHC);
		
	}
</script>
@endpush