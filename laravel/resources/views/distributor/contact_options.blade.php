@extends("distributor.page")
@include("BladeFunctions")
@section("content")
@php
  $Distributor = \App\Models\Distributor::whereCode(Request()->distributor)->with('ContactMethods','CustomerContactMethods','CustomersContactMethods')->first();
  //dd($Distributor->toArray());
@endphp

<div class="content"><form method="post">{{ csrf_field() }}
    <div class="panel panel-default">
	  <div class="panel-heading"><strong>Contact methods - {{ $Distributor->name }}</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('distributor.index'):url()->previous()) !!}</div>
	  <div class="panel-body">
		<div class="clearfix">
		  <div class="col-xs-6">
			<h4 class="text-center"><u>Distributor Contact methods</u></h4>
			{!! formGroup(2,'email','select','Email','',['selectOptions' => ['Yes','No']]) !!}
			{!! formGroup(2,'sms','select','SMS','',['selectOptions' => array_merge([''=>'None'],\App\Models\SMSGateway::pluck('name','code')->toArray())]) !!}
		  </div>
		  <div class="col-xs-6">
			<h4 class="text-center"><u>Customer's Contact methods</u></h4>
			{!! formGroup(2,'customer_email','select','Email','',['selectOptions' => ['Yes','No']]) !!}
			{!! formGroup(2,'customer_sms','select','SMS','',['selectOptions' => array_merge([''=>'None'],\App\Models\SMSGateway::pluck('name','code')->toArray())]) !!}
		  </div>
		</div><br><br>
		<div class="panel-heading"><strong class="panel-title">Customer Exception List</strong></div>
		<div class="panel-body">
		  <div class="clearfix">
			<div class="col-xs-5">
			  <select multiple name="ex_customers[]" id="ex_customers">
				@php $Customers = \App\Models\Distributor::find(Request()->distributor)->get_all_customers();  @endphp
				@forelse($Customers as $Customer) <option value="{{ $Customer->code }}">{{ $Customer->name }}</option> @empty @endforelse
			  </select>
			</div>
			<div class="col-xs-7" style="border-left: 1px solid #DDD;">
			  <div class="table-responsive"><table class="table table-striped" id="ex_criteria"><caption>Exception Criteria</caption><thead><tr><th>Customer</th><th>Email</th><th>SMS</th></tr></thead><tbody>

			  </tbody></table></div>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="panel-footer clearfix">
		<input type="submit" name="submit" value="Update All Details" class="btn btn-primary pull-right">
	  </div>
  </div></form>
</div>

@endsection
@push("js")
<script type="text/javascript" src="js/multiselect.js"></script>
<script type="text/javascript" src="js/quicksearch.js"></script>
<script type="text/javascript">
$(function(){ $('#ex_customers').multiSelect({
	selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off'>",
	selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off'>",
	afterInit: function(ms){
			var that = this,
					$selectableSearch = that.$selectableUl.prev(),
					$selectionSearch = that.$selectionUl.prev(),
					selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
					selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

			that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
			.on('keydown', function(e){
				if (e.which === 40){
					that.$selectableUl.focus();
					return false;
				}
			});

			that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
			.on('keydown', function(e){
				if (e.which == 40){
					that.$selectionUl.focus();
					return false;
				}
			});
		},
		afterSelect: function(V){
			this.qs1.cache();
			this.qs2.cache();
			AddCustomerException(V);
		},
		afterDeselect: function(V){
			this.qs1.cache();
			this.qs2.cache();
			RemoveCustomerException(V);
		}
}); });
function AddCustomerException(V){
  N = GetCustomerName(V);
  AddTblRow(N,V);
}
function GetCustomerName(V){
  return $('#ex_customers option[value="'+V+'"]').text();
}
function AddTblRow(N,V){
  TBD = $('table#ex_criteria tbody');
  $('<tr>').attr({'data-customer':V}).html([$('<th>').addClass('ex_name').text(N),$('<td>').addClass('ex_email').html(exEmailSelectObj(V)),$('<td>').addClass('ex_sms').html(exSMSSelectObj(V))]).appendTo(TBD);
}
function exEmailSelectObj(V){
  AlterValue = {'No':'Yes','Yes':'No'}; Template = $('[name="customer_email"]'); DefaultValue = Template.val();
  return Template.clone().attr({name:'exEmail['+V+']'}).val(AlterValue[DefaultValue]);
}
function exSMSSelectObj(V){
  Template = $('[name="customer_sms"]');
  Value = '';//(Template.val() == "") ? Template.find('option:eq(1)').val() : "";
  return Template.clone().attr({name:'exSms['+V+']'}).val(Value);
}
function RemoveCustomerException(V){
  $('tr[data-customer="'+V+'"]').remove();
}
function SetDistributorContactMethods(email,sms){
  $('[name="email"]').val(email); $('[name="sms"]').val(sms);
}
function SetDistributorCustomerContactMethods(email,sms){
  $('[name="customer_email"]').val(email); $('[name="customer_sms"]').val(sms);
}
function SetCustomersContactMethods(Ary){
	ms = $('#ex_customers');
	$.each(Ary,function(i,ary){
		ms.multiSelect('select',ary[0]);
		SetExCustomerContactMethod(ary[0],ary[1],ary[2])
	})
}
function SetExCustomerContactMethod(c,e,s){
	$('[name="exEmail['+c+']"]').val(e); $('[name="exSms['+c+']"]').val(s)
}
$(function(){ @if($Distributor->ContactMethods) SetDistributorContactMethods('{{ $Distributor->ContactMethods->email }}','{{ $Distributor->ContactMethods->sms }}'); @endif
	@if($Distributor->CustomerContactMethods) SetDistributorCustomerContactMethods('{{ $Distributor->CustomerContactMethods->email }}','{{ $Distributor->CustomerContactMethods->sms }}'); @endif
	@if($Distributor->CustomersContactMethods) SetCustomersContactMethods({!! $Distributor->CustomersContactMethods->map(function($item){ return [$item->customer,$item->email,$item->sms]; })->toJson() !!}); @endif
})

</script>
@endpush
@push("css")
<link rel="stylesheet" href="css/multiselect.css" type="text/css">
@endpush