@extends("ebis.page")
@include('BladeFunctions')
@php $ORM = new \App\Models\eBis; $DORM = $ORM->query(); @endphp
@php if(Request()->search_text){
    $st = '%'.Request()->search_text.'%';
    $DORM = $DORM->where(function($Q)use($st){ $Q->where('customer','like',$st)->orWhere('code','like',$st); })
                ->orWhereHas('Customer',function($q)use($st){ $q->where('name','like',$st); })
                ->orWhereHas('Subscriptions',function($q)use($st){ $q->where('domain','like',$st)->orWhere('start','like',$st)->orWhere('end','like',$st)->orWhere('package','like',$st); })
                ;
} @endphp
@php $Data = $DORM->paginate(30); @endphp

@section("content")
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading"><strong>ePlus Business Information System</strong>{!! PanelHeadAddButton(Route('ebis.new'),'Add New') !!}</div>
            <div class="panel-body">
                <div class="clearfix pagination">
                    <div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
                    <div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
                </div>
                @if($Data->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>Code</th><th width="40%">Customer & Product</th><th>Subscriptions</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                            @foreach($Data as $eBis)
                                <tr>
                                    <td>{{ $eBis->code }}</td>
                                    <td>{{ $eBis->Customer->name }}<br />{{ $eBis->product }}</td>
                                    <td>{!! getFormattedSubscriptions($eBis->Subscriptions) !!}</td>
                                    <td>{{ $eBis->status }}</td>
                                    <td nowrap>{!! ActionsToListIcons($eBis) !!}</td>
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
    function getFormattedSubscriptions($records){
        return $records->map(function($record){ return date('d/M/Y',strtotime($record->start)) . " >> " . date('d/M/Y',strtotime($record->end)) . " >> " . $record->status; })->implode('<br />');
    }
    function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'ebis',$PK = 'id',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
        $LI = [];
        foreach($Obj->$Prop as $act){
            if(in_array($act,$Obj->$Modal)) continue;
            $LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
        }
        return implode('',$LI);
    }
    function Scopes($Obj){ //var_dump($Obj); return;
        $scope_keys = ['public','support','distributor','customer'];
        $scopes = [];
        foreach($scope_keys as $key) if($Obj && $Obj->$key === 'YES') $scopes[] = ucfirst($key);
        if($Obj && $Obj->partner) $scopes[] = $Obj->Partner->name;
        return implode(", ", $scopes);
    }
@endphp
