@extends("ebis.page")
@include('BladeFunctions')
@php $Data = App\Models\eBis::with(['Subscriptions','Customer'])->find(request()->id); @endphp
@php //dd($Data->toArray()) @endphp
@php $Details = ['Code' => 'code', 'Customer Code' => 'customer','Customer Name' => ['Customer','name'], 'Product' => 'product'] @endphp
@section("content")
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading"><strong>{{ $Data->Customer->name }}</strong>{!! PanelHeadBackButton(Route('ebis.index')) !!}</div>
            <div class="panel-body">
                <div class="table-responsive"><table class="table table-striped"><tbody>
                        @foreach($Details as $TH => $TD)
                        <tr><th>{{ $TH }}</th><th>:</th><td>{{ array_get($Data,implode(".",is_array($TD)?$TD:[$TD])) }}</td></tr>
                        @endforeach
                        </tbody></table></div>
                <hr>
                <h4>Subscriptions</h4>
                <div class="table-responsive"><table class="table table-striped"><thead><tr><th>No</th><th>Package</th><th>Host</th><th>Start Date</th><th>End Date</th><th>Status</th><th>&nbsp;</th></tr></thead><tbody>
                        @foreach($Data->Subscriptions as $subscription)
                            <tr><td>{{ $loop->iteration }}</td>
                            @foreach(['package','host','start','end','status'] as $Field)
                                <td>{{ $subscription->$Field }}</td>
                            @endforeach
                                <td><form method="post" action="{!! route('ebis.cancel',$subscription->id) !!}">{{ csrf_field() }}<input type="submit" value="Cancel" class="btn btn-warning btn-sm" /></form></td>
                            </tr>
                        @endforeach
                        </tbody></table></div>
            </div>
        </div>
    </div>

@endsection
