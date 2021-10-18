@extends("sk.page_features")
@include('BladeFunctions')
@include('sk.functions')
@php $ORM = new \App\Models\SK\Feature; $DORM = $ORM->query(); @endphp
@php if(Request()->search_text){
    $st = '%'.Request()->search_text.'%';
    $DORM = $DORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('id','like',$st)->orWhere('detail','like',$st); })
        ;
} @endphp
@php $Data = $DORM->paginate(30); @endphp
@section("content")
    <div class="content">
        <div class="text-right" style="margin-bottom: 5px">
            <a class="btn btn-info text-right" href="{!! route('sk.index') !!}">Clients</a>
            <a class="btn btn-info text-right" href="{!! route('sk.editions') !!}">Editions</a>
        </div>
        @unless(Request()->search_text)
        <div class="row">
            <div class="col-xs-12 col-md-9">@component('sk.comp.add_feature') @endcomponent</div>
            <div class="hidden-sm hidden-xs col-md-3">
                GLOBAL_FEATURES <div class="small">For development</div>
                <textarea class="form-control" rows="17">{!! \App\Models\SK\Feature::whereStatus('Active')->get()->map(function($f){ return [$f->code,$f->type,$f->default]; })->toJson() !!}</textarea>
            </div>
        </div>
        @endunless
        <div class="panel panel-primary">
            <div class="panel-heading"><strong>Features</strong></div>
            <div class="panel-body">
                <div class="clearfix pagination">
                    <div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
                    <div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
                </div>
                @if($Data->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead><tr><th>Feature</th><th>Details</th><th>Type</th><th>Default</th><th>&nbsp;</th></tr></thead>
                            <tbody>
                            @foreach($Data as $row)
                                <tr><td><div class="small">{{ $row->code }}</div>{{ $row->name }}</td><td>{{ $row->detail }}</td><td>{{ $row->type }}</td><td>{{ $row->default }}</td><td><a href="{!! route('sk.feature.editions',['feature' => $row->id]) !!}" class="btn btn-info">Details</a></td></tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>@else
                    <div class="jumbotron">
                        <h2 class="text-center">No Records found</h2>
                    </div>@endif
            </div>
        </div>
    </div>
@endsection
