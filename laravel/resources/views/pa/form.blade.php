@extends("pa.page")
@include('BladeFunctions')
@section("content")
@php
$Data = (Request()->code) ? \App\Models\PublicArticle::find(Request()->code) : null;
@endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading"><strong class="panel-title">Public Article</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('pa.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				{!! formGroup(2,'code','text','Article Code',old('code',($Data)?$Data->code:(new \App\Models\PublicArticle)->NewCode())) !!}
				{!! formGroup(2,'name','text','Article Name',old('name',($Data)?$Data->name:'')) !!}
				{!! formGroup(2,'title','text','Page Title',old('title',($Data)?$Data->title:'')) !!}
				{!! formGroup(2,'view','text','View/Template Name',old('view',($Data)?$Data->view:'')) !!}
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Submit" class="btn btn-primary pull-right">
			</div>
		</div>
	</div>
</form></div>

@endsection