@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Closure')->whereCode(Request()->tkt)->first() @endphp
@php //dd($Data->toArray())
@endphp
<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Ticket Closure</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(1, 'solution', 'textarea', 'Solution Provided' ,($Data->Closure)?$Data->Closure->solution:'', ['style' => 'height:150px;']) !!}
					{!! formGroup(1, 'reference_ticket', 'text', 'Referred ticket, if any', ($Data->Closure)?$Data->Closure->reference_ticket:'') !!}
					<div class="tkt_dets"{{ ($Data->Closure && $Data->Closure->reference_ticket) ? ' style="display: none"' : ''}}><pre><strong></strong><br></pre></div>
					{!! formGroup(1, 'support_doc', 'file', 'Attach Support Document') !!}
				</div>
				<div class="col col-md-6">
					<table class="table table-striped"><tbody>
						<tr><th>Code</th><th>:</th><td>{{ $Data->code }}</td></tr>
						<tr><th>Title</th><th>:</th><td>{{ $Data->title }}</td></tr>
						<tr><th>Description</th><th>:</th><td>{{ $Data->description }}</td></tr>
						<tr><th>Created On</th><th>:</th><td>{{ date('D d/m, h:i A',strtotime($Data->created_at)) }} - <small>({{ Sec2Ago(time()-strtotime($Data->created_at)) }})</small></td></tr>
						<tr><th>Closed On</th><th>:</th><td>{{ date('D d/m, h:i A',$Data->Cstatus->start_time) }} - <small>({{ Sec2Ago(time()-$Data->Cstatus->start_time) }})</td></tr>
						<tr><th>Current Status</th><th>:</th><td>{{ $Data->Cstatus->status }}</td></tr>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
		<input type="hidden" name="fb" value="NO">
			<input type="submit" name="submit" value="Submit Details" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection
@php
function Sec2Ago($s){
	if(60 > $s) return $s . ' secs'; $d = 60;
	if(3600 > $s) { $min = floor($s/$d); $sec = $s%$d; return join(" ",[$min,'mins',$sec,'secs']); } $d = 3600;
	if(86400 > $s) { $hrs = floor($s/$d); $min = floor(($s%$d)/60); return join(" ",[$hrs,'hrs',$min,'mins']); } $d = 86400;
	if(2592000 > $s) { $dys = floor($s/$d); $hrs = floor(($s%$d)/3600); return join(" ",[$dys,'days',$hrs,'hrs']); }
	$d = 2592000;	$mn = floor($s/$d); $dys = floor(($s%$d)/86400); return join(" ",[$mn,'months',$dys,'days']);
}
@endphp
@push('js')
<script type="text/javascript">
	$(function(){
		$('[name="reference_ticket"]').bind('blur',function(){
			tkt = $('[name="reference_ticket"]').val(); if($.trim(tkt) == '') return $('.tkt_dets').slideUp();
			FireAPI('api/v1/tkt/get/td/'+tkt,function(TJ){
				$('.tkt_dets').slideDown().find('pre').html($('<strong>').text([TJ.code,TJ.title,TJ.cstatus.status].join('::'))).append($('<br>')).append(TJ.description);
			})
		}).trigger('blur');@if($Data->Closure && $Data->Closure->support_doc)
			$('.form-group:last').append($('<label class="checkbox-inline">').html([
				$('<input type="checkbox" name="dcd" value="YES">'),
				'Delete current Document'
			])).append($('<a>').attr('href','{{ Route("download.support.doc",$Data->Closure->id) }}').text('or Download {{ (json_decode($Data->Closure->support_doc,true))['name'] }}').css('margin-left','20px'));
		@endif
	})
	function DownloadSupportDoc(id){
		FireAPI('api/v1/tkt/get/dsd/'+id);
	}
</script>
@endpush