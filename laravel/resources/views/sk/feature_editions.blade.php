@extends("sk.page_features")
@include('BladeFunctions')
@include('sk.functions')
@php $Feature = \App\Models\SK\Feature::with(['Editions','Parent'])->find($feature); @endphp
@php $Editions = \App\Models\SK\Edition::whereStatus('Active')->where('id','>',1)->get(); @endphp
@php $EVals = $Feature->Editions->pluck('value','edition')->toArray(); @endphp
@section("content")
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading"><span>{{ $Feature->name }} (<span class="small">{{ $Feature->code }}</span>)</span></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{!! route('sk.feature.editions',['feature' => $Feature->id]) !!}" method="post">{{ csrf_field() }}
                            {!! formGroup(2,'code','text','Code',old('code',$Feature->code)) !!}
                            @if($errors && $errors->has('code')) <a class="small" onClick="getFeatureCode()" style="position: absolute; top:8px; right:40px; cursor: pointer">Generate New</a> @endif
                            {!! formGroup(2,'name','text','Name',$Feature->name) !!}
                            {!! formGroup(2,'detail','textarea','Details',$Feature->detail) !!}
                            {!! formGroup(2,'type','select','Type',$Feature->type,['selectOptions' => ['Yes/No','Detail']]) !!}
                            {!! formGroup(2,'default','text','Default',$Feature->default) !!}
                            {!! formGroup(2,'parent','select','Parent',$Feature->parent,['selectOptions' => parentFeatures(),'attr' => 'onChange="parentChanged(this)"']) !!}
                            {!! formGroup(2,'status','select','Status',$Feature->type,['selectOptions' => ['Active','Inactive']]) !!}
                            <div class="text-right"><input type="submit" name="submit" value="Update Feature Details" class="btn btn-warning"> &nbsp; &nbsp;</div>
                            {!! formGroup(1,'level','hidden',null,old('level',$Feature->level)) !!}
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="{!! route('sk.feature.editions',['feature' => $Feature->id]) !!}" method="post">{{ csrf_field() }}
                            <div class="table-responsive">
                                <table class="table table-condensed table-hover table-bordered">
                                    <thead><tr><th>Edition</th><th>Value</th></tr></thead>
                                    <tbody>
                                    @if($Feature->type === 'Yes/No')
                                        @foreach($Editions as $row)
                                            <tr><td>{{ $row->name }}</td><td>
                                                    <select name="feature[{{ $row->id }}]" class="form-control"><option value="Yes" @if(\Illuminate\Support\Arr::get($EVals,$row->id,$Feature->default) === 'Yes') selected @endif>Yes</option><option value="No" @if(\Illuminate\Support\Arr::get($EVals,$row->id,$Feature->default) === 'No') selected @endif>No</option></select>
                                                </td></tr>
                                        @endforeach
                                    @else
                                        @foreach($Editions as $row)
                                            <tr><td>{{ $row->name }}</td><td>
                                                    <input name="feature[{{ $row->id }}]" class="form-control" type="text" value="{{ \Illuminate\Support\Arr::get($EVals,$row->id,$Feature->default) }}">
                                                </td></tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right"><input type="submit" name="submit" value="Update Edition Values" class="btn btn-warning"> </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function getFeatureCode(){
            inp = $('input[name="code"]'); inp.val('');
            $.get('{!! route('sk.feature.code') !!}',function(r){
                inp.val(r)
            })
        }
        function parentChanged(e){
            inp = $('input[name="level"]'); inp.val('');
            inp.val(parseInt(e.selectedOptions[0].getAttribute('level'))+1)
        }
    </script>
@endpush
