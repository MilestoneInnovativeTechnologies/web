@extends("sk.page")
@include('BladeFunctions')
@include('sk.functions')
@php $Branch = \App\Models\SK\Branch::with(['Subscriptions.Edition','Subscription.Edition','Edition','Features'])->find($branch); @endphp
@php $Features = \App\Models\SK\Feature::whereStatus('Active')->get(); @endphp
@php $BranchFeatures = $Branch->Features->mapWithKeys(function($feature){ return [$feature->id => $feature->pivot->value]; }); @endphp
@section("content")
    <div class="content">
        <div class="text-right" style="margin-bottom: 5px">
            <a class="btn btn-info text-right" href="{!! route('sk.detail',['id' => $Branch->client]) !!}">Branches</a>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Subscription</div>
                    @if($Branch->Subscription)<div class="panel-body">
                        <div class="row">
                            <div class="col-md-3"><input type="text" value="{{ $Branch->key  }}" class="form-control"></div>
                            <div class="col-md-3"><input type="text" readonly value="{{ $Branch->Subscription->Edition->name  }}" class="form-control"></div>
                            <div class="col-md-3"><input type="text" readonly value="{{ $Branch->Subscription->expiry  }}" class="form-control"></div>
                            <div class="col-md-3"><input type="text" readonly value="{{ $Branch->Subscription->remarks  }}" class="form-control"></div>
                            <div class="col-md-12" style="margin-top: 5px"><textarea class="form-control" rows="5">{{ $Branch->Subscription->code }}</textarea></div>
                        </div>
                        <form action="{!! route('sk.cancel',['subscription' => $Branch->Subscription->id]) !!}" method="post">{{ csrf_field() }}<input type="submit" name="submit" value="Cancel Subscription" class="btn btn-danger pull-right" style="margin-top: 5px"></form>
                    </div>@else
                        <div class="text-center text-warning" style="padding: 10px 0px">No Active Subscription Exists</div>
                    @endif
                    <div class="panel-heading">Add New Subscription</div>
                    @if($Branch->key)
                        <form action="{!! route('sk.subscription',['branch' => $Branch->id]) !!}" method="post">{{ csrf_field() }}
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4"><input type="text" value="{{ $Branch->key }}" readonly class="form-control"></div>
                                    <div class="col-md-4"><input type="text" value="{{ $Branch->Edition->name }}" readonly class="form-control"></div>
                                    <div class="col-md-4"><input type="text" value="{{ $Branch->date }}" readonly class="form-control"></div>
                                </div><br />
                                {!! formGroup(2,'expiry','text','Expire On',date('Y-m-d',strtotime('+1 month')),['attrs' => 'placeholder="yyyy-mm-dd"']) !!}
                                {!! formGroup(2,'remarks','text','Remarks','') !!}
                            </div>
                            <div class="panel-footer text-right">
                                <input type="submit" name="submit" value="Add Subscription" class="btn btn-default">
                            </div>
                        </form>
                    @else
                        <div class="panel-body text-center">
                            <form action="{!! route('sk.key',['branch' => $Branch->id]) !!}" method="post">{{ csrf_field() }}
                                <input type="submit" name="submit" value="Generate Key" class="btn btn-default">
                            </form>
                        </div>
                    @endif
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Previous Subscriptions</div>
                    <div class="panel-body">
                        @if($Branch->Subscriptions->where('status','Expired')->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-condensed table-hover table-bordered">
                                    <thead><tr><th>Date</th><th>Edition</th><th>Remarks</th><th>Expiry</th></tr></thead>
                                    <tbody>
                                    @foreach($Branch->Subscriptions->where('status','Expired') as $sub)
                                        <tr>
                                            <td>{{ $sub->code_date }}</td>
                                            <td>{{ $sub->Edition->name }}</td>
                                            <td>{{ $sub->remarks }}</td>
                                            <td>{{ $sub->expiry }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center">No Data</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <form action="{!! route('sk.branch_detail',['branch' => $Branch->id]) !!}" method="post">{{ csrf_field() }}
                    <div class="panel panel-default">
                        <div class="panel-heading">Update Branch</div>
                        <div class="panel-body">
                            {!! formGroup(2,'name','text','Name',$Branch->name) !!}
                            {!! formGroup(2,'code','text','Code',$Branch->code) !!}
                            {!! formGroup(2,'serial','text','Serial',$Branch->serial) !!}
                            {!! formGroup(2,'edition','select','Edition',$Branch->edition,['selectOptions' => \App\Models\SK\Edition::where('status','Active')->get()->map(function($edt){ return ['text' => $edt->name,'value' => $edt->id]; })->toArray()]) !!}
                            {!! formGroup(2,'ip_address','text','IP',$Branch->ip_address) !!}
                            {!! formGroup(2,'status','select','Status',$Branch->status,['selectOptions' => ['Active','Inactive']]) !!}
                            <br /><strong>Database</strong><br /><br />
                            {!! formGroup(2,'hostname','text','Host',$Branch->hostname) !!}
                            {!! formGroup(2,'db_port','text','Port',$Branch->db_port) !!}
                            {!! formGroup(2,'db_username','text','User',$Branch->db_username) !!}
                            {!! formGroup(2,'db_password','text','Pass',$Branch->db_password) !!}
                        </div>
                        <div class="panel-footer text-right">
                            <input type="submit" name="submit" value="Update Branch Details" class="btn btn-default">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <form action="{!! route('sk.branch_features',['branch' => $Branch->id]) !!}" method="post">{{ csrf_field() }}
            <div class="panel panel-default">
                <div class="panel-heading">Features</div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered">
                            <thead><tr><th>Feature</th><th>Detail</th><th>Default</th><th>Value</th></tr></thead>
                            <tbody>
                            @foreach($Features as $feature)
                                <tr>
                                    <td><div class="small">{{ $feature->code }}</div>{{ $feature->name }}</td>
                                    <td>{{ $feature->detail }}</td>
                                    <td>{{ $feature->default }}</td>
                                    <td>
                                        @if($feature->type === 'Yes/No')
                                            <select name="feature[{{ $feature->id }}]" class="form-control"><option value="Yes" @if(\Illuminate\Support\Arr::get($BranchFeatures,$feature->id,$feature->default) === 'Yes') selected @endif>Yes</option><option value="No" @if(\Illuminate\Support\Arr::get($BranchFeatures,$feature->id,$feature->default) === 'No') selected @endif>No</option></select>
                                        @else
                                            <input name="feature[{{ $feature->id }}]" class="form-control" type="text" value="{{ \Illuminate\Support\Arr::get($BranchFeatures,$feature->id,$feature->default) }}">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <input type="submit" name="submit" class="btn btn-warning" value="Update Features">
                </div>
            </div>
        </form>
    </div>
@endsection
