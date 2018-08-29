@extends("ppo.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\PublicPrintObject::find($code); @endphp

<div class="content">
	<form method="post" enctype="multipart/form-data">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Update Print Object Details</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ppo.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="col-xs-6">
					{!! formGroup(2,'code','text','Code',$Data->code) !!}
					{!! formGroup(2,'name','text','Name',$Data->name) !!}
					{!! formGroup(2,'description','textarea','Description',$Data->description) !!}
				</div>
				<div class="col-xs-6" style="padding: 0px">
					@php $Specs = (new \App\Models\PublicPrintObjectSpecs)->specs; if(!empty($Specs)) { @endphp
					@foreach($Specs as $DBField => $NameOptionsArray)
						<div class="col-xs-12" style="padding: 0px;" title="{{ $NameOptionsArray[0] }}">{!! formGroup(2,$DBField,'select',$NameOptionsArray[0],($Data->Specs && $Data->Specs->details && array_key_exists($NameOptionsArray[0],$Data->Specs->details))?$Data->Specs->details[$NameOptionsArray[0]]:'',['labelStyle' => 'text-align:left', 'selectOptions' => $NameOptionsArray[1]]) !!}</div>
					@endforeach
					@php } @endphp
				</div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Update" class="btn btn-primary pull-right">
			</div>
		</div>
	</form>
</div>

@endsection