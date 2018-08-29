@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php  @endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}@if(Request()->srq) <input type="hidden" name="srq" value="{{ Request()->srq }}"> @endif
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Support Tickets</strong></div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6 left_section">
					@unless(session()->get('_rolename') == 'customer')
					{!! formGroup(2,'customer','text','Customer',old('customer'),['labelWidth'	=>	4, 'attr'	=>	'onChange=LoadCustomerProducts(this.value)']) !!}
					@endunless
					{!! formGroup(2,'product','select','Product',old('product'),['labelWidth'	=>	4, 'attr'	=>	'onChange=\'ProductChanged()\' required']) !!}
					{!! formGroup(2,'category','select','Category',old('category'),['labelWidth'	=>	4, 'selectOptions' => ['' => 'Other']]) !!}
				</div>
				<div class="col col-md-6" style="border-left:1px solid #CECECE">
					{!! formGroup(1,'title','text','Issue Title',old('title'),['labelWidth'	=>	4]) !!}
					{!! formGroup(1,'description','textarea','Issue Description',old('description',''),['labelWidth'	=>	4, 'style'	=>	'height:160px']) !!}
					<table class="table table-striped attachments"><thead><tr><th colspan="2">Attachments</th><th><a href="javascript:AddAttachment()" class="btn btn-default btn-xs pull-right"><span class="glyphicon glyphicon-plus"></span></a></th></tr></thead><tbody>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Submit Ticket" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection
@push('js')
<script type="text/javascript">
var __CUSTOMER = '@if(session()->get("_rolename") == "customer"){{ Auth()->user()->partner }}@endif';
</script>
<script type="text/javascript" src="js/ticket_create.js"></script>
@endpush