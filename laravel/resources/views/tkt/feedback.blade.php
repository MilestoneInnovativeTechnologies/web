@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Feedback')->whereCode(Request()->tkt)->first() @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Feedback Form</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					@include('tkt.feedback_form')
				</div>
				<div class="col col-md-6">
					<table class="table table-striped"><tbody>
						<tr><th>Code</th><td>{{ $Data->code }}</td></tr>
						<tr><th>Title</th><td>{{ $Data->title }}</td></tr>
						<tr><th>Description</th><td>{{ $Data->description }}</td></tr>
						<tr><th>Created On</th><td>{{ date('D d/m, h:i A',strtotime($Data->created_at)) }} - <small>({{ Sec2Ago(time()-strtotime($Data->created_at)) }})</small></td></tr>
						<tr><th>Closed On</th><td>{{ date('D d/m, h:i A',strtotime($Data->Cstatus->created_at)) }} - <small>({{ Sec2Ago(time()-strtotime($Data->Cstatus->created_at)) }})</td></tr>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Submit Feedback" class="btn btn-primary pull-right">
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
@push('css')
<style type="text/css">
	p.form-control-static { height: 50px; width: 220px; background: url(img/rating_colors.gif) no-repeat; background-position: -220px 0px; text-indent: -5000px; color: #FFF; padding: 0px; margin: 0px; transition: background-position-x 0.2s linear; }
	p.form-control-static .ahold { height: 50px; width: 220px; background: url(img/rating_star.png) no-repeat right; }
	p.form-control-static a { display: block; float: left; width: 20px; height: 50px; padding: 0px; margin: 0px; border: 0px; text-decoration: none; overflow: hidden; }
</style>
@endpush
@push('js')
<script type="text/javascript">
	$(function(){
		rp = $('p.form-control-static'); rating = parseInt(rp.text()); ah = rp.html($('<div class="ahold">')).find('.ahold');
		i=0; while(i<=10) ah.append($('<a>').attr('href','javascript:rate('+(i++)+')').text(' '));
		rate(rating);
	})
	function rate(n){
		n = parseInt(n); px = n*20;
		$('p.form-control-static').css('background-position-x',px-200);
		$('[name="points"]').val(n)
	}
</script>
@endpush