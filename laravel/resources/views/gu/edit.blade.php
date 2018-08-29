@extends("gu.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Update Form</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('gu.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				{!! formGroup(2,'name','text','Name',old('name',$gu->name)) !!}
				{!! formGroup(2,'description','textarea','Description',old('description',$gu->description)) !!}
				{!! formGroup(2,'customer','text','Customer',old('customer',$gu->customer),['attr' => 'placeholder="customer code if any"']) !!}
				{!! formGroup(2,'ticket','text','Ticket',old('ticket',$gu->ticket),['attr' => 'placeholder="ticket code if any"']) !!}
				{!! formGroup(2,'overwrite','select','Overwritable',old('overwrite',$gu->overwrite),['selectOptions' => ['N' => 'No', 'Y' => 'Yes']]) !!}
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Update Form" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection