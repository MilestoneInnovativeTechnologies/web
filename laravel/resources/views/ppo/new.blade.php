@extends("ppo.page")
@include('BladeFunctions')
@section("content")
@php $ORM = new \App\Models\PublicPrintObject; @endphp

<div class="content">
	<form method="post" enctype="multipart/form-data">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Add New Print Object</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ppo.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="clearfix">
					<div class="col-xs-6">
						{!! formGroup(2,'code','text','Code',$ORM->NewCode(),['labelStyle' => 'text-align:left', 'attr' => 'placeholder=\'Leave empty for generating new code on submit\'']) !!}
						{!! formGroup(2,'name','text','Name',['labelStyle' => 'text-align:left', 'attr' => 'required']) !!}
						{!! formGroup(2,'description','textarea','Description',['labelStyle' => 'text-align:left']) !!}
					</div>
					<div class="col-xs-6">
						{!! formGroup(1,'file','file','File',['labelStyle' => 'text-align:left', 'attr' => 'required']) !!}
						{!! formGroup(1,'preview','file','Preview Image',['labelStyle' => 'text-align:left']) !!}
					</div>
				</div>
				<hr><div class="h4">Specifications</div>
				@php $Specs = (new \App\Models\PublicPrintObjectSpecs)->specs; if(!empty($Specs)) { @endphp
				@foreach($Specs as $DBField => $NameOptionsArray)
					<div class="col-xs-4">{!! formGroup(2,$DBField,'select',$NameOptionsArray[0],['labelStyle' => 'text-align:left', 'selectOptions' => $NameOptionsArray[1]]) !!}</div>
				@endforeach
				@php } @endphp
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Submit" class="btn btn-primary pull-right">
			</div>
		</div>
	</form>
</div>

@endsection