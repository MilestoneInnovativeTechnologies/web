@extends("sk.page")
@include('BladeFunctions')
@php $client = \App\Models\SK\Client::with(['Branches.Edition'])->withoutGlobalScopes()->find($id); @endphp
@section("content")

    <div class="content">
        <div class="text-right" style="margin-bottom: 5px">
            <a class="btn btn-default text-right" href="{!! route('sk.index') !!}">Back</a>
        </div>
        <div class="row">
            <div class="col-md-9">
                <form action="{!! route('sk.new_branch',['client' => $client->id]) !!}" method="post">{{ csrf_field() }}
                <div class="panel panel-default">
                    <div class="panel-heading"><span>Add New Branch</span></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">{!! formGroup(1,'name','text','Branch Name',old('name','')) !!}</div>
                            <div class="col-md-6">{!! formGroup(1,'code','text','Branch Code',old('code','')) !!}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">{!! formGroup(1,'edition','select','Edition',old('name',''),['selectOptions' => \App\Models\SK\Edition::where('status','Active')->get()->map(function($edt){ return ['text' => $edt->name,'value' => $edt->id]; })->toArray()]) !!}</div>
                            <div class="col-md-4">{!! formGroup(1,'date','text','Date',old('date',date('Y-m-d')),['attr' => 'placeholder=yyyy-mm-dd']) !!}</div>
                            <div class="col-md-4">{!! formGroup(1,'serial','text','Device Serial',old('serial','')) !!}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">{!! formGroup(1,'ip_address','text','Branch IP',old('ip_address','')) !!}</div>
                            <div class="col-md-6">{!! formGroup(1,'hostname','text','Host Name',old('host',''),['attr' => 'placeholder="sk.dyndns.org"']) !!}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">{!! formGroup(1,'db_username','text','DB Username',old('db_username','')) !!}</div>
                            <div class="col-md-4">{!! formGroup(1,'db_password','text','DB Password',old('db_password','')) !!}</div>
                            <div class="col-md-4">{!! formGroup(1,'db_port','number','DB Port',old('db_port','3306')) !!}</div>
                        </div>
                    </div>
                    <div class="panel-footer clearfix">
                        <div class="pull-left">{!! formGroup(1,'client','hidden','',old('client',array_get($client,'id'))) !!}</div>
                        <input type="submit" name="submit" value="Add Branch" class="btn btn-info pull-right">
                    </div>
                </div>
                </form>
            </div>
            <div class="col-md-3">
                <form action="{!! route('sk.detail',['id' => $client->id]) !!}" method="post">{{ csrf_field() }}
                    <div class="panel panel-default">
                        <div class="panel-heading">Update Company</div>
                        <div class="panel-body">
                            {!! formGroup(2,'name','text','Name',$client->name) !!}
                            {!! formGroup(2,'code','text','Code',$client->code) !!}
                            {!! formGroup(2,'status','select','Status',$client->status,['selectOptions' => ['Active','Inactive']]) !!}
                        </div>
                        <div class="panel-footer text-right">
                            <input type="submit" name="submit" value="Update Client" class="btn btn-default">
                        </div>
                    </div>
                </form>
            </div>
        </div>

<!--        <div class="panel panel-info">
            <div class="panel-heading">Branches</div>
            <div class="panel-body">-->
                @if($client->Branches->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover table-bordered">
                            <thead><tr><th>Branch</th><th width="40%">Edition</th><th>Host/IP Address</th><th> </th></tr></thead>
                            <tbody>
                            @foreach($client->Branches as $branch)
                                <tr>
                                    <td>{{ $branch->name }}<div class="small">{{ $branch->code }}</div></td>
                                    <td>{{ $branch->Edition->name }}<div class="small">{{ $branch->date }}</div><div class="small text-primary">{{ $branch->status }}</div></td>
                                    <td>Host: {{ $branch->hostname }}<br />IP: {{ $branch->ip_address }}<br />IP Date: {{ $branch->ip_address_date }}</td>
                                    <td><a href="{!! route('sk.branch_detail',['branch' => $branch->id]) !!}" class="btn btn-info">Details</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>@else
                    <div class="jumbotron">
                        <h2 class="text-center">No Branches</h2>
                    </div>@endif
{{--            </div>
        </div>--}}
    </div>
@endsection
