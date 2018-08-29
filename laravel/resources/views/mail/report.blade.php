@extends("mail.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Mail::find(Request()->code); @endphp
@php  @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Email Sent/Delivery Reports - <small>{{ $Data->subject }}</small></strong>{!! PanelHeadButton('javascript:LoadLog(\''.$Data->code.'\')','Reload Data','stats','warning') !!}<span class="pull-right"> &nbsp; </span>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('mail.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="col-md-6"><div class="table-responsive"><table class="table table-striped table-condensed sent_report" style="font-size: 12px;"><caption>Sent Report</caption><thead><tr><th>Time</th><th>Sender</th><th>Rceivers</th></tr></thead><tbody>
				
			</tbody></table></div></div>
			<div class="col-md-6" style="padding-left: 0px"><div class="table-responsive"><table class="table table-striped table-condensed receipt_report" style="font-size: 12px;"><caption>Receipt Report</caption><thead><tr><th>No</th><th>Receiver</th><th>Opened Count</th><th>Opened at</th></tr></thead><tbody>
				
			</tbody></table></div></div>
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript">
function LoadLog(code){
	FireAPI('api/v1/mail/get/log',function(D){
		PopulateSent(D);
		PopulateReceipt(D)
	},{code:code});
}
function PopulateSent(D){
	Sent = D.sent; Sender = D.sender; Receivers = D.receivers;
	Batch = Sender2Batch(Sender); console.log(Batch);
	Tbl = $('table.sent_report tbody').empty();
	$.each(Batch,function(i,Obj){
		TR = $('<tr>').appendTo(Tbl); StartTime = Object.keys(Obj)[0]; DateArray = Time2Date(StartTime);
		TD1 = $('<td>').html([$('<strong>').text(DateArray[0]),$('<br>'),$('<strong>').text(DateArray[1])]);
		TD2 = $('<td>').text(Obj[StartTime]); RNames = [];
		$.each(Obj,function(Time,Name){
			RNames = RNames.concat(Receivers[Time]);
		})
		TD3 = $('<td>').html($('<ol>').css({ margin: '0px',paddingLeft:'15px' }).html($('<li>').html(RNames.join('</li><li>'))));
		TR.html([TD1,TD2,TD3]);
	})
}
function PopulateReceipt(D){
	Receipt = D.receipt; count = 0;
	Tbl2 = $('table.receipt_report tbody').empty();
	$.each(Receipt,function(Name,TimeArray){
		TR = $('<tr>').appendTo(Tbl2);
		TD0 = $('<td>').text(++count);
		TD1 = $('<td>').html(Name);
		TD2 = $('<td>').text(TimeArray.length);
		TD3 = $('<td>').html($('<ol>').css({ margin:'0px',paddingLeft:'15px' }).html(TimeLIs(TimeArray)));
		TR.html([TD0,TD1,TD2,TD3]);
	})
}
function Sender2Batch(D){
	Batch = []; PTime = 0; PName = ''; Dif = 30;
	$.each(D,function(Time,Name){
		Time = parseInt(Time);
		if(Time-PTime > Dif || PName != Name){	Batch[Batch.length] = {};	}
		Batch[Batch.length-1][Time] = Name;
		PTime = Time; PName = Name;
	});
	return Batch;
}
function Time2Date(T){
	date = new Date(parseInt(T)*1000);
	d1a = date.toDateString().split(" "); date1 = [d1a[2],d1a[1],d1a[3]].join('/');
	d2a = date.toTimeString().split(" "); date2 = d2a[0];
	return [date1,date2];
}
function TimeLIs(TA){
	HTML = [];
	$.each(TA,function(i,T){
		TD = Time2Date(T); TDStr = TD.join(' ');
		HTML.push($('<li>').text(TDStr));
	});
	return HTML;
}
$(function(){
	LoadLog('{{ $Data->code }}')
})
</script>
@endpush