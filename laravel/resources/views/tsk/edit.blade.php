@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Edit Task Details</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?(Route('tsk.index')):(url()->previous())) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(2,'status','static','Current active status', $tsk->status) !!}
					{!! formGroup(2,'title','text','Task Title', old('title',$tsk->title)) !!}
					{!! formGroup(2,'description','textarea','Description', old('description',$tsk->description), ['style'	=>	'height:120px']) !!}
					{!! formGroup(2,'support_type','select','Support Type', old('support_type',$tsk->support_type), ['selectOptions'	=>	array_merge([''	=>	'None'],\App\Models\SupportType::pluck('name','code')->toArray())]) !!}
					<div class="form-group clearfix form-horizontal">
						<label class="control-label col-xs-3">Handle method</label>
						<div class="col-xs-9">
							<label class="radio-inline"><input type="radio" name="handle_after" value=""{{ (!$tsk->handle_after)?' checked':'' }}> Immediate</label>
							<label class="radio-inline"><input type="radio" name="handle_after" value="after_tasks"{{ ($tsk->handle_after)?' checked':'' }}> After Tasks</label>
						</div>
					</div>
					<div class="form-group clearfix form-horizontal">
						<label class="control-label col-xs-3">Available Tasks</label>@if($tsk->handle_after) @php $my_befores = $tsk->handle_after->pluck('id')->toArray(); @endphp @endif
						<div class="col-xs-9">@foreach($tsk->Ticket->Tasks as $tskObj)
							@if($tsk->seqno > $tskObj->seqno)
							<div class="checkbox"><label><input type="checkbox" name="after_tasks[]" value="{{ $tskObj->id }}"@if(isset($my_befores) && in_array($tskObj->id,$my_befores)) checked @endif>{{ $tskObj->title }}</label></div>
							@endif
						@endforeach</div>
					</div>
				</div>
				<div class="col col-md-6">@php $tkt = $tsk->Ticket; @endphp
					<div class="table table-responsive">
						<table class="table table-striped">
							<tbody>
								<tr><th colspan="2">Ticket Details</th></tr>
								<tr><th>Ticket Code</th><td>{{ $tkt->code }}</td></tr>
								<tr><th>Customer</th><td>{{ $tkt->Customer->name }}</td></tr>
								<tr><th>Priority</th><td>{{ $tkt->priority }}</td></tr>
								<tr><th>Title</th><td>{{ $tkt->title }}</td></tr>
								<tr><th>Description</th><td>{{ $tkt->description }}</td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Update Changes" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection