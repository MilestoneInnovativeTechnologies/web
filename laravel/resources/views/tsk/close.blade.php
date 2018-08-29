@extends("tsk.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Closing Task</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?(Route('tsk.index')):(url()->previous())) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-9">
					<h4><u>Are you sure, Have you done with this task?</u></h4>
					<h4>{{ $Task->title }}</h4>
					<h5>{!! nl2br($Task->description) !!}</h5>
				</div>
				<div class="col-md-3">
					<a href="{{ Route('tsk.close',['tsk'=>$Task->id,'confirm'=>'yes']) }}" title="Close Task" class="btn btn-none btn-primary jumbotron" style="color:#FFF; width: 100%; padding-right:0px; padding-left:0px; margin-bottom: 5px">Yes, Close task now.</a>
				</div>
			</div>
			<div class="col-xs-9"></div>
			<div class="col-xs-3">
				
			</div>
		</div>
	</div>
</div>

@endsection