@extends("vacancy.page")
@include('BladeFunctions')
@section("content")
@php
$Data = \App\Models\Vacancy::withCount('Applicants')->get();
//dd($Data->toArray())
@endphp
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading"><strong>Vacancies</strong>{!! PanelHeadAddButton(Route('vacancy.create'),'Add new Vacancy') !!}</div>
            <div class="panel-body">
                @if($Data->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>No</th><th>Details</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($Data as $Vacancy)
                                <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $Vacancy->title }}</strong><br>{!! nl2br($Vacancy->description) !!}</td><td><strong>Live: </strong>{{ ["No","Yes"][$Vacancy->live] }}<br><strong>Status: </strong>{{ $Vacancy->status }}<br><strong>Views: </strong>{{ $Vacancy->views }}<br><strong>Applicants: </strong>{{ $Vacancy->applicants_count }}</td><td>{{ date("D d/M/Y",strtotime($Vacancy->date)) }}</td><td>{!! ActionsToListIcons($Vacancy) !!}</td></tr>
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
    function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'vacancy',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
        $LI = [];
        foreach($Obj->$Prop as $act){
            if(in_array($act,$Obj->$Modal)) continue;
            $LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
        }
        return implode('',$LI);
    }
@endphp
