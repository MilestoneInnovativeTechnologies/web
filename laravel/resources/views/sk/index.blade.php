@extends("sk.page")
@include('BladeFunctions')
@php $ORM = new \App\Models\SK\Client; $DORM = $ORM->query()->withoutGlobalScopes()->with(['Partner','Branches'])->latest(); @endphp
@php if(Request()->search_text){
    $st = '%'.Request()->search_text.'%';
    $DORM = $DORM->where(function($Q)use($st){ $Q->where('partner','like',$st)->orWhere('name','like',$st)->orWhere('id','like',$st); })
        ->orWhereHas('Partner',function($q)use($st){ $q->where('name','like',$st); })
        ->orWhereHas('Branches',function($q)use($st){ $q->where('name','like',$st); })
        ;
} @endphp
@php $Data = $DORM->paginate(30); @endphp
@section("content")
    <div class="content">
        <div class="text-right" style="margin-bottom: 5px">
            <a class="btn btn-info text-right" href="{!! route('sk.features') !!}">Features</a>
            <a class="btn btn-info text-right" href="{!! route('sk.editions') !!}">Editions</a>
        </div>
        @unless(Request()->search_text) @component('sk.comp.add_client') @endcomponent @endunless
        <div class="panel panel-primary">
            <div class="panel-heading"><strong>Clients</strong>{{--{!! PanelHeadAddButton(Route('ebis.new'),'Add New') !!}--}}</div>
            <div class="panel-body">
                <div class="clearfix pagination">
                    <div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
                    <div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
                </div>
                @if($Data->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead><tr><th>Client</th><th width="40%">Branches</th><th>Detail</th></tr></thead>
                            <tbody>
                            @foreach($Data as $sk)
                                <tr>
                                    <td><div class="small">{{ $sk->code }}</div>{{ $sk->name }}<br /><br /><div class="small">{{ $sk->Partner->code }}<br />{{ $sk->Partner->name }}</div></td>
                                    <td>{{ $sk->status }}</td>
                                    <td nowrap>{!! ActionsToListIcons($sk) !!}</td>
                                </tr>
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
@php
    function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'sk',$PK = 'id',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
        $LI = [];
        foreach($Obj->$Prop as $act){
            if(in_array($act,$Obj->$Modal)) continue;
            $LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
        }
        return implode('',$LI);
    }
@endphp
