@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Team','Customer','Product','Edition','Createdby')->whereCode(Request()->tkt)->first() @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Reassign Ticket - {{ $Data->code }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(2,'customer_name','text','Customer',$Data->Customer->name, ['labelWidth' => 4, 'attr' => 'readonly']) !!}
					{!! formGroup(2,'product_name','text','Product',$Data->Product->name, ['labelWidth' => 4, 'attr' => 'readonly']) !!}
					{!! formGroup(2,'edition_name','text','Edition',$Data->Edition->name, ['labelWidth' => 4, 'attr' => 'readonly']) !!}
					{!! formGroup(2,'current_team','text','Current Support Team',$Data->Team->Team->name, ['labelWidth' => 4, 'attr' => 'readonly']) !!}
					{!! formGroup(2,'team','select','New Support Team',$Data->Team->team,['labelWidth' => 4, 'selectOptions' => \App\Models\SupportTeam::pluck('name','code')]) !!}
					<input type="hidden" name="customer" value="{{ $Data->customer }}">
				</div>
				<div class="col col-md-6">
					<table class="table table-striped"><tbody>
						<tr><th>Title</th><td>{{ $Data->title }}</td></tr>
						<tr><th>Description</th><td>{{ $Data->description }}</td></tr>
						<tr><th>Created On</th><td>{{ date('D d/m, h:i A',strtotime($Data->created_at)) }}</td></tr>
						<tr><th>Created By</th><td>{{ $Data->Createdby->name }}</td></tr>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Reassign Support Team" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection