@extends("sreq.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Respond to Service Request</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('sreq.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="well">{!! nl2br($sr->message) !!}</div>
				<div class="row rsp">
					<div class="col-md-6" style="transition: all 0.3s ease" onMouseOver="AlterDiv(0,[8,4])" onMouseOut="AlterDiv(0,[6,6])"><a href="{{ Route('tkt.create',['srq' => $sr->id]) }}" class="btn btn-primary btn-block" style="padding: 50px 12px; word-break: break-all">Create a Support Ticket</a></div>
					<div class="col-md-6" style="transition: all 0.3s ease" onMouseOver="AlterDiv(0,[4,8])" onMouseOut="AlterDiv(0,[6,6])"><textarea name="response" class="form-control" style="height: 120px;" placeholder="Enter response here">@if($sr->response){{ $sr->response }}@endif</textarea></div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Set Response" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection
@push('js')
<script type="text/javascript">
function AlterDiv(d,a){
	$('.row.rsp div:eq('+d+')').attr('class','col-md-'+a[0]).siblings().attr('class','col-md-'+a[1])
}
</script>
@endpush