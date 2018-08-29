@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\Ticket::with('Team','Customer','Product','Edition','Createdby')->whereCode(Request()->tkt)->first() @endphp

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Recreate Ticket - {{ $Data->code }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index',['tkt'=>$Data->code]):url()->previous()) !!}</div>
		<div class="panel-body">
			{!! formGroup(2,'title','text','Title','[REOPENED] ['.$Data->code.'] '.$Data->title, ['labelWidth' => 3]) !!}
			{!! formGroup(2,'description','textarea','Description',$Data->description, ['labelWidth' => 3, 'style' => 'height:140px;']) !!}
			<input type="hidden" name="customer" value="{{ $Data->customer }}">
			<input type="hidden" name="product" value="{{ $Data->seqno }}">
			<input type="hidden" name="category" value="{{ $Data->category }}">

		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Recreate Ticket" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection