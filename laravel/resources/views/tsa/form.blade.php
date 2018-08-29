@extends("tsa.page")
@include('BladeFunctions')
@section("content")
@php $Partner = \App\Models\Partner::find(\Auth()->user()->partner) @endphp
@php $Update = isset($Update) @endphp

<div class="content"><form method="post" action="{{ ($Update)?Route('tsa.update',['code'=>$Agent->code]):Route('tsa.store') }}">{!! ($Update)?csrf_field().method_field('put'):csrf_field() !!}
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{{ ($Update)?'Edit':'New'}} Support Agent</strong>{!! PanelHeadBackButton(Route('tsa.index')) !!}</div>
				<div class="panel-body">
					@unless($Partner->Roles->contains('name','supportteam'))
					{!! formGroup(2,'parent','select','Support Agent',old('parent',($Update)?$Agent->Team->parent:''), ['selectOptions'	=>	\App\Models\SupportTeam::pluck('name','code')]) !!}
					@endunless
					{!! formGroup(2,'name','text','Agent Name',old('name',($Update)?$Agent->name:'')) !!}
					{!! formGroup(2,'email','text','Agent Email',old('email',($Update)?$Agent->Logins->first()->email:'')) !!}
					{!! formGroup(2,'phone','text','Agent Phone',old('phone',($Update)?$Agent->Details->phone:''),['attr'	=>	'placeholder="number without country code"']) !!}
				</div>
				<div class="panel-footer clearfix">
					<input type="submit" class="btn btn-primary pull-right" name="submit" value="{{ ($Update)?'Update Data':'Save Data'}}">
				</div>
			</div>
		</div>
	</div></form>
</div>

@endsection