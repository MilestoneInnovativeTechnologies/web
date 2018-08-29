@extends("ppo.page")
@include('BladeFunctions')
@section("content")
@php $ORM = new \App\Models\PublicPrintObject; $DORM = $ORM->query(); @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $DORM = $DORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhere('description','like',$st)->orWhereHas('Specs',function($Q)use($st){ foreach(range(0,9) as $C){ $N = 'spec'.$C; if($C) $Q->orWhere($N,'like',$st); else $Q->where($N,'like',$st); } }); }); } @endphp
@php $Data = $DORM->paginate(30); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Public Print Objects</strong>@if(in_array('new',$ORM->available_actions)) {!! PanelHeadAddButton(Route('ppo.new'),'Add a new') !!} @endif</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
				@foreach($Data->chunk(3) as $ChunkArray)
					<div class="row">
					@foreach($ChunkArray as $Item)
						<div class="col col-md-4">
							<div class="item">
								<div class="clearfix top">
									<div class="h4 pull-left">{{ $Item->name }}</div>
									@if(in_array('new',$ORM->available_actions))<div class="h4 pull-right" title="Downloads">{{ $Item->downloads }}</div>@endif
								</div>
								<div class="preview" style="background-image: url('{{ \Storage::disk($ORM->storage_disk)->url($Item->preview) }}')">
									<div class="aholder">
										@if($Item->preview)<a href="{{ \Storage::disk($ORM->storage_disk)->url($Item->preview) }}" class="btn btn-default" target="_blank">Preview</a>@endif
										@if($Item->Specs && $Item->Specs->details) <ul>
											@foreach($Item->Specs->details as $Name => $Value)
												<li title="{{ $Name }}"><span class="glyphicon glyphicon-chevron-right"></span> &nbsp; {{ $Name }} &gt; {{ $Value }}</li>
											@endforeach
										</ul> @endif
									</div>
								</div>
								<div class="clearfix base">
									<small class="pull-left">{!! nl2br($Item->description) !!}</small>
									<div class="actions pull-right">
										{!! ActionsToListIcons($Item) !!}
									</div>
								</div>
							</div>
						</div>
					@endforeach
					</div>
				@endforeach
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
		</div>
	</div>
</div>

@endsection
@php
function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'ppo',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
@endphp
@push('css')
	<style type="text/css" rel="stylesheet">
		.preview { height: 20em; background-repeat: no-repeat; background-size: contain; background-position: center; border: 1px solid #EEE; overflow: hidden; }
		.preview .aholder { height:inherit; padding-top:4em;  background-color: rgba(220,220,220,0.35); transform: scale(0,0); transition: all 0.1s; }
		.preview .aholder a { margin-left: 40%; }
		.preview .aholder ul { margin-top: 1em; background-color: rgba(221,221,221,0.0); list-style: none; font-size: 0.85em; padding: 1em 2em; transition: all 0.3s 0.1s; }
		.preview:hover .aholder ul { background-color: rgba(221,221,221,0.9); }
		.preview:hover .aholder { transform: scale(1,1); }
		small { margin-top: 0.6em; max-width: 50%; }
		.base { max-height: 0px; overflow: hidden; transition: all 0.5s; }
		.item:hover .base { max-height: 100px; }
	</style>
@endpush