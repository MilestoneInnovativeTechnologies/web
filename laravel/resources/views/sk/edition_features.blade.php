@extends("sk.page_features")
@include('BladeFunctions')
@include('sk.functions')
@php $Features = \App\Models\SK\Feature::whereStatus(['Active'])->with('Children.Children')->whereNull('parent')->get(); @endphp
@php $Edition = \App\Models\SK\Edition::with('Features')->find($edition); @endphp
@php $FVals = $Edition->Features->pluck('value','feature')->toArray(); @endphp
@section("content")
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading">{{ $Edition->name }}</div>
            <div class="panel-body">
                <form action="{!! route('sk.edition.features',['edition' => $Edition->id]) !!}" method="post">{{ csrf_field() }}
                <div class="row">
                    <div class="col-md-5">
                        {!! formGroup(2,'name','text','Name',$Edition->name,['attr' => 'placeholder="Name"']) !!}
                        {!! formGroup(2,'status','select','Status',$Edition->status,['selectOptions' => ['Active','Inactive']]) !!}
                    </div>
                    <div class="col-md-5">{!! formGroup(1,'detail','textarea','Details',$Edition->detail,['attr' => 'placeholder="Details"']) !!}</div>
                    <div class="col-md-2"><br /><br /><input type="submit" name="submit" value="Update Edition Details" class="btn btn-default"></div>
                </div>
                </form>
            </div>
            <div class="panel-heading">Features</div>
            <form action="{!! route('sk.edition.features',['edition' => $Edition->id]) !!}" method="post">{{ csrf_field() }}
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead><tr><th>Feature</th><th>Type</th><th>Default</th><th>Value</th></tr></thead>
                            <tbody>
                            @foreach($Features as $row)
                                <tr>
                                    <td>@if($row->Parent) {{ $row->Parent->name }}  > @endif{{ $row->name }}</td><td>{{ $row->type }}</td><td>{{ $row->default }}</td><td>
                                        @if($row->type === 'Yes/No') <select name="feature[{{ $row->id }}]" class="form-control"><option value="Yes" @if(\Illuminate\Support\Arr::get($FVals,$row->id,$row->default) === 'Yes') selected @endif>Yes</option><option value="No" @if(\Illuminate\Support\Arr::get($FVals,$row->id,$row->default) === 'No') selected @endif>No</option></select> @else <input type="text" class="form-control" name="feature[{{ $row->id }}]" value="{{ \Illuminate\Support\Arr::get($FVals,$row->id,$row->default) }}"> @endif
                                    </td></tr>
                                @if($row->Children && $row->Children->isNotEmpty())
                                    @foreach($row->Children as $row1)
                                        <tr><td>{{ $row->name }}  > {{ $row1->name }}</td><td>{{ $row1->type }}</td><td>{{ $row1->default }}</td><td>
                                                @if($row1->type === 'Yes/No') <select name="feature[{{ $row1->id }}]" class="form-control"><option value="Yes" @if(\Illuminate\Support\Arr::get($FVals,$row1->id,$row1->default) === 'Yes') selected @endif>Yes</option><option value="No" @if(\Illuminate\Support\Arr::get($FVals,$row1->id,$row1->default) === 'No') selected @endif>No</option></select> @else <input type="text" class="form-control" name="feature[{{ $row1->id }}]" value="{{ \Illuminate\Support\Arr::get($FVals,$row1->id,$row1->default) }}"> @endif
                                            </td></tr>
                                        @if($row1->Children && $row1->Children->isNotEmpty())
                                            @foreach($row1->Children as $row2)
                                                <tr><td>{{ $row->name }} > {{ $row1->name }} > {{ $row2->name }}</td><td>{{ $row2->type }}</td><td>{{ $row2->default }}</td><td>
                                                        @if($row2->type === 'Yes/No') <select name="feature[{{ $row2->id }}]" class="form-control"><option value="Yes" @if(\Illuminate\Support\Arr::get($FVals,$row2->id,$row2->default) === 'Yes') selected @endif>Yes</option><option value="No" @if(\Illuminate\Support\Arr::get($FVals,$row2->id,$row2->default) === 'No') selected @endif>No</option></select> @else <input type="text" class="form-control" name="feature[{{ $row2->id }}]" value="{{ \Illuminate\Support\Arr::get($FVals,$row2->id,$row2->default) }}"> @endif
                                                    </td></tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <input type="submit" name="submit" value="Update Feature Values" class="btn btn-info">
                </div>
            </form>
        </div>
    </div>
@endsection
