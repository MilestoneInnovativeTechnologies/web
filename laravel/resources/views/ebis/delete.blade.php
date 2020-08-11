@extends("ebis.page")
@include('BladeFunctions')
@php $Data = App\Models\eBis::with(['Subscriptions','Customer'])->find(request()->id); @endphp
@php //dd($Data->toArray()) @endphp
@php $Details = ['Code' => 'code', 'Customer Code' => 'customer','Customer Name' => ['Customer','name'], 'Product' => 'product', 'Status' => 'status'] @endphp
@section("content")
    <div class="content">
        <form method="post">{{ csrf_field() }}
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Delete eBis for {{ $Data->Customer->name }} ??</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ebis.index'):url()->previous()) !!}</div>
                <div class="panel-body">
                    <div class="table-responsive"><table class="table table-striped"><tbody>
                            @foreach($Details as $TH => $TD)
                                <tr><th>{{ $TH }}</th><th>:</th><td>{{ array_get($Data,implode(".",is_array($TD)?$TD:[$TD])) }}</td></tr>
                            @endforeach
                            </tbody></table>
                    </div>
                    <h4>Subscriptions</h4>
                    <div class="table-responsive"><table class="table table-striped"><thead><tr><th>No</th><th>Package</th><th>Domain</th><th>Start Date</th><th>End Date</th><th>Status</th></tr></thead><tbody>
                            @foreach($Data->Subscriptions as $subscription)
                                <tr><td>{{ $loop->iteration }}</td>
                                    @foreach(['package','domain','start','end','status'] as $Field)
                                        <td>{{ $subscription->$Field }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody></table></div>
                </div>
                <div class="panel-footer clearfix">
                    {!! PanelFooterButton('Delete','danger') !!}
                </div>
            </div>
        </form>
    </div>

@endsection
