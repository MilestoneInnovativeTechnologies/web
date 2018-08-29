@extends("faq.page")
@include('BladeFunctions')
@php
$Faqs = \App\Models\FAQ::all();
//dd($Faqs->toArray());
@endphp
@php $ORM = new \App\Models\FAQ; $DORM = $ORM->query(); @endphp
@php if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $DORM = $DORM->where(function($Q)use($st){ $Q->where('question','like',$st)->orWhere('id','like',$st)->orWhere('answer','like',$st); }); } @endphp
@php $Data = $DORM->paginate(30); @endphp
@section("content")
<div class="content">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>FAQs</strong>{!! PanelHeadAddButton(Route('faq.create'),'Create new FAQ') !!}</div>
        <div class="panel-body">
            <div class="clearfix pagination">
                <div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
                <div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
            </div>
            @if($Data->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>No</th><th width="40%">Details</th><th>Interactions</th><th>Categories</th><th>Scope</th><th>Tags</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                        @foreach($Data as $Faq)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{!! nl2br($Faq->question) !!}</strong><br>{!! nl2br($Faq->answer) !!}</td>
                                <td><strong>Views</strong>: {{ $Faq->views }}<br><strong>Benefits</strong>: {{ $Faq->benefits }}<br></td>
                                <td>{{ ($Faq->Categories) ? implode(", ",$Faq->Categories->categories) : '' }}</td>
                                <td>{{ Scopes($Faq->Scope) }}</td>
                                <td>{{ implode(", ",$Faq->tags) }}</td>
                                <td>{{ $Faq->status }}</td>
                                <td nowrap>{!! ActionsToListIcons($Faq) !!}</td>
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
    function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'faq',$PK = 'id',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
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