@extends("packages.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Latest Packages</strong>{!! PanelHeadButton('revert','Revert a package','scissors') !!} <span class="pull-right"> &nbsp; </span>{!! PanelHeadButton('package/upload','Upload a package','cloud-upload') !!}</div>
		<div class="panel-body">@if(!empty($Data))
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Product</th><th>Edition</th><th>Package</th><th>Latest Approved Version</th><th>Build Date</th><th>Actions</th></tr></thead>
					<tbody></tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript">
	_Data = {!! json_encode($Data) !!};
	$(function(){
		tbd = $('tbody').empty(); $P = 0;
		$.each(_Data, function(PID,PAry){
			TR = $('<tr>').appendTo(tbd);
			ITD = NTD(++$P,TR); PRTD = NTD(PAry['name'],TR); $E = 0;
			$.each(PAry['editions'], function(EID, EAry){
				if($E++){ TR = $('<tr>').appendTo(tbd); IRS([ITD,PRTD]); }
				EDTD = NTD(EAry['name'],TR); $K = 0;
				$.each(EAry['packages'], function(KID, KAry){
					if($K++){ TR = $('<tr>').appendTo(tbd); IRS([ITD,PRTD,EDTD]); }
					PKTD = NTD(KAry['name'],TR);
					VER = KAry['version'];
					if(VER){
						VRTD = NTD((VER['version_numeric'])?(VER['version_numeric']):'-',TR);
						DTTD = NTD((VER['build_date'])?ReadableDate(VER['build_date']):'-',TR);
						ACTD = NTD(ACTBTS(PID,EID,KID,VER['version_sequence']),TR);
					} else {
						NTD('-',TR); NTD('-',TR); NTD(ACTBTS(PID,EID,KID,null),TR);
					}
				})
			})
		})
	})
	function NTD(T,TR){
		return $('<td>').html(T).appendTo(TR).attr({rowspan:1}).css('vertical-align','middle');
	}
	function IRS(TD){
		if($.isArray(TD)) $.each(TD, function(i,mTD){ IRS(mTD); })
		else {
			R = parseInt(TD.attr('rowspan'));
			TD.attr('rowspan',++R);
		}
	}
	function ACTBTS(PID,EID,KID,SEQ){
		return [
			(SEQ)?btn('Download Package',['package','download',PID,EID,KID,SEQ].join('/'),'cloud-download').append(' Download This').addClass('btn-info').prop('download',true):'',
			$('<span>').text(' '),
			btn('Upload Package',['package','upload',PID,EID,KID].join('/'),'cloud-upload').append(' Upload New').addClass('btn-info'),
		]
	}
</script>
@endpush