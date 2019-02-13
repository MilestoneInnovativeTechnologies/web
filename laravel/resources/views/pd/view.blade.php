@extends("pd.page")
@include('BladeFunctions')
@php $Data = App\Models\PD::with('Tables')->find(request()->id); @endphp
@php //dd($Data->toArray()) @endphp
@php $Details = ['Customer Code' => 'customer', 'Product' => 'product', 'PD Code' => 'code', 'Web Address' => 'url_web', 'API URL' => 'url_api', 'Interact URL' => 'url_interact', 'Service Start Date' => 'date_start', 'Service End Date' => 'date_end', 'Current Status' => 'status'] @endphp
@section("content")
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading"><strong>{{ $Data->Customer->name }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('pd.index'):url()->previous()) !!}</div>
            <div class="panel-body">
                <div class="table-responsive"><table class="table table-striped"><tbody>
                        @foreach($Details as $TH => $TD)
                        <tr><th>{{ $TH }}</th><th>:</th><td>{{ array_get($Data,implode(".",is_array($TD)?$TD:[$TD])) }}</td></tr>
                        @endforeach
                        </tbody></table></div>
                <hr>
                <h4>Tables</h4>
                <div class="table-responsive"><table class="table table-striped"><thead><tr><th>No</th><th>Table</th><th>Last Created</th><th>Last Updated</th></tr></thead><tbody>
                        @foreach($Data->Tables as $TH => $TD)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $TD->table }}</td><td>{{ $TD->last_created }}</td><td>{{ $TD->last_updated }}</td></tr>
                        @endforeach
                        </tbody></table></div>
            </div>
        </div>
    </div>

@endsection