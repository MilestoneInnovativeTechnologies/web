@extends("sk.page_features")
@include('BladeFunctions')
@include('sk.functions')
@php $Data = \App\Models\SK\Edition::where('id','>',1)->get(); @endphp
@section("content")
    <div class="content">
        <div class="text-right" style="margin-bottom: 5px">
            <a class="btn btn-info text-right" href="{!! route('sk.index') !!}">Clients</a>
            <a class="btn btn-info text-right" href="{!! route('sk.features') !!}">Features</a>
        </div>
        <div class="row">
            <div class="col-md-9">
                @if($Data->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead><tr><th>Edition</th><th>Details</th><th>Status</th><th>&nbsp;</th></tr></thead>
                            <tbody>
                            @foreach($Data as $row)
                                <tr><td>{{ $row->name }}</td><td>{{ $row->detail }}</td><td>{{ $row->status }}</td><td><a href="{!! route('sk.edition.features',['edition' => $row->id]) !!}" class="btn btn-info">Details</a></td></tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>@else
                    <div class="jumbotron">
                        <h2 class="text-center">No Records found</h2>
                    </div>@endif
            </div>
            <div class="col-xs-12 col-md-3">@component('sk.comp.add_edition') @endcomponent</div>
        </div>
    </div>
@endsection
