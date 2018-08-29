@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Feedback')->whereCode(Request()->tkt)->first() @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Done or Complete Ticket</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					<h4>You are about to make the ticket {{ $Data->code }} as COMPLETED.</h4>
					@unless($Data->Feedback || session()->get('no_feedback'))
					<h4>Would you like to provide a feedback before closing? <a href="javascript:Feedback()" class="btn btn-primary fdbtn">YES</a></h4>
					<div class="feedback_form" style="display: none">@include('tkt.feedback_form')</div>
					@endunless
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
		<input type="hidden" name="fb" value="NO">
			<input type="submit" name="submit" value="Complete Ticket" class="btn btn-primary pull-right sbtn">
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
	function Feedback(){
		A = {'YES':['NO','Submit Feedback and Complete Ticket'],'NO':['YES','Complete Ticket']}
		FB = $('.fdbtn'); SB = $('.sbtn'); SB.val(A[FB.text()][1]); $('[name="fb"]').val(FB.text()); FB.text(A[FB.text()][0]);
		$('.feedback_form').slideToggle(150);
	}
</script>
@endpush