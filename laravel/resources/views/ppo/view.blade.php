@extends("ppo.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\PublicPrintObject::find($code); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Data->name }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ppo.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>
					<tr><th>Description</th><th>:</th><td nowrap>{!! nl2br($Data->description) !!}</td><td>&nbsp;</td><th rowspan="{{ ($Data->Specs && $Data->Specs->details) ? (count($Data->Specs->details)+9) : '7' }}">@if($Data->preview) <img class="img-responsive" src="{{ \Storage::disk($Data->storage_disk)->url($Data->preview) }}">@endif</th></tr>
					<tr><th>Created By</th><th>:</th><td nowrap>{{ ($Data->CreatedBy) ? $Data->CreatedBy->name : '' }}</td><td>&nbsp;</td></tr>
					<tr><th>Created On</th><th>:</th><td nowrap>{{ date('D - d/M/Y',strtotime($Data->created_at))  }}</td><td>&nbsp;</td></tr>
					<tr><th>Downloads</th><th>:</th><td nowrap>{{ $Data->downloads }}</td><td>&nbsp;</td></tr>
					@if($Data->Specs && $Data->Specs->details)
						<tr><th colspan="4">&nbsp;</th></tr>
						<tr><th colspan="4">Specifications</th></tr>
						@foreach($Data->Specs->details as $Name => $Value)
							<tr><th>{{ $Name }}</th><th>:</th><td nowrap>{{ $Value }}</td><td>&nbsp;</td></tr>
						@endforeach
					@endif
					<tr><th colspan="4" rowspan="3">&nbsp;</td></tr>
					</tbody></table></div>
		</div>
	</div>
</div>

@endsection