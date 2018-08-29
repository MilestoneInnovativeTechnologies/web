@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Category','Type','Customer','Cstatus','Product','Edition')->whereCode(Request()->tkt)->first(); /*dd($Data->toArray());*/ @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Entitle ticket - {{ $Data->code }} - {{ $Data->Cstatus->status }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			@if(true || in_array($Data->Cstatus->status,['NEW','OPENED']))
			<div class="row">
				<div class="col col-md-6 edit left_section" style="border-right: 1px solid #DDDDDD">
					{!! formGroup(2,'ticket_type','select','Ticket type',$Data->type?$Data->Type->code:'',['labelWidth' => 4,'selectOptions'=>array_merge(['' => 'None'],\App\Models\TicketType::whereStatus('ACTIVE')->pluck('name','code')->toArray())]) !!}
					{!! formGroup(2,'priority','select','Ticket priority',$Data->priority,['labelWidth' => 4,'selectOptions' => ['VERY LOW','LOW','NORMAL','HIGH','VERY HIGH']]) !!}
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-4">Current Category</label><div class="col-xs-8" style="padding-top: 8px"><strong>@if($Data->category){{ $Data->Category->name }}@else Other @endif</strong></div></div>
					@if($Data->category && $Data->Category->Specs && $Data->Category->Specs->isNotEmpty()) @foreach($Data->Category->Specs as $k => $spec)
					<div class="form-group clearfix form-horizontal"><label class="control-label col-xs-4">{{ $spec->name }}</label><div class="col-xs-8" style="padding-top: 8px">@if($Data->Category_specs->isNotEmpty() && $Data->Category_specs->has($k)) {{ ($Data->Category_specs[$k]->value_text)?:$Data->Category_specs[$k]->Value->name }} @endif</div></div>
					@endforeach @endif
					{!! formGroup(2,'category','select','New Category',$Data->category,['labelWidth' => 4, 'selectOptions' => ['' => 'Other']]) !!}
				</div>
				<div class="col col-md-6">
					<table class="table table-striped">
						<tbody>
							<tr><th>Customer</th><td>{{ $Data->Customer->name }}</td></tr>
							<tr><th>Product</th><td>{{ $Data->Product->name }} {{ $Data->Edition->name }} Edition</td></tr>
							<tr><th>Ticket Code</th><td>{{ $Data->code }}</td></tr>
							<tr><th>Created On</th><td><script>document.write(ReadableDate('{{ $Data->created_at }}'))</script></td></tr>
							<tr><th>Ticket Status</th><td>{{ $Data->Cstatus->status }} <small>(<script>document.write(ReadableDate('{{ $Data->Cstatus->created_at }}'))</script>)</small></td></tr>
							<tr><th>Title</th><td><strong>{{ $Data->title }}</strong></td></tr>
							<tr><th>Description</th><td>{!! nl2br($Data->description) !!}</td></tr>
						</tbody>
					</table>
				</div>
			</div>
			@else
			<div class="jumbotron text-center"><h4>Only NEW or OPENED tickets are available for modification</h4></div>
			@endif
		</div>@if(true || in_array($Data->Cstatus->status,['NEW','OPENED']))
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Update details" class="btn btn-primary pull-right">
		</div>@endif
	</div></form>
</div>

@endsection
@push('js')
<script type="text/javascript">
var __CUSTOMER = '{{ $Data->customer }}';
@if($Data->category && $Data->Category_specs && $Data->Category_specs->isNotEmpty()) var __CSPEC = {@foreach($Data->Category_specs as $cspec)
'{{ $cspec->Specification->code }}':'{{ ($cspec->value_text)?:$cspec->Value->code }}'@if($loop->remaining),@endif @endforeach}; @endif
$(function(){ LoadProductCategories(__CUSTOMER,'{{ $Data->seqno }}'); })
</script>
<script type="text/javascript" src="js/category_management.js"></script>
@endpush