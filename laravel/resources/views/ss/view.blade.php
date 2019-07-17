@extends("ss.page")
@include('BladeFunctions')
@php $Data = App\Models\SmartSale::with(['Tables','Customer'])->find(request()->id); @endphp
@php //dd($Data->toArray()) @endphp
@php $Details = ['Customer Code' => 'customer','App Customer Name' => 'name', 'Company Brief'=>'brief', 'Product' => 'product', 'SS Code' => 'code', 'Web Address' => 'url_web', 'API URL' => 'url_api', 'Interact URL' => 'url_interact', 'Service Start Date' => 'date_start', 'Service End Date' => 'date_end', 'Current Status' => 'status'] @endphp
@section("content")
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading"><strong>{{ $Data->Customer->name }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ss.index'):url()->previous()) !!}</div>
            <div class="panel-body">
                <div class="table-responsive"><table class="table table-striped"><tbody>
                        @foreach($Details as $TH => $TD)
                        <tr><th>{{ $TH }}</th><th>:</th><td>{{ array_get($Data,implode(".",is_array($TD)?$TD:[$TD])) }}</td></tr>
                        @endforeach
                        <tr><th>Image</th><th>:</th><td><img src="{{ $Data->image }}"></td></tr>
                        </tbody></table></div>
                <hr>
                <h4>Tables</h4>
                <div class="table-responsive"><table class="table table-striped"><thead><tr><th>No</th><th>Table</th><th>Type</th><th>Delay</th><th>Last Synced</th><th>Latest Record Date</th></tr></thead><tbody>
                        @foreach($Data->Tables as $Table)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $Table->table }}</td>
                            @foreach(['type','delay','sync','record'] as $Field)
                                <td>{{ $Table->$Field }}</td>
                            @endforeach
                            </tr>
                        @endforeach
                        </tbody></table></div>
            </div>
        </div>
    </div>

@endsection