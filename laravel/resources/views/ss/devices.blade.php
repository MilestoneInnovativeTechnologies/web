@extends("ss.page")
@include('BladeFunctions')
@php $Data = App\Models\SmartSale::with(['Devices'])->find(request()->id); @endphp
@php //dd($Data->toArray()) @endphp
@php $Details = ['Name' => 'name','UUID' => 'uuid','IMEI'=>'imei','Serial'=>'serial'] @endphp
@section("content")
    <div class="content">
        <div class="row">
            <div class="col col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Registered Devices</strong></div>
                    <div class="panel-body">
                        <div class="table-responsive"><table class="table table-striped table-condensed"><thead>
                                <tr>@foreach($Details as $th => $td) <th>{{ $th }}</th>@endforeach<th>Other Codes</th><th>&nbsp;</th></tr>
                                </thead><tbody>
                                @forelse($Data->devices as $device)
                                    <tr>
                                        @foreach($Details as $td) <td>{{ $device->$td }}</td> @endforeach
                                        <td>{{ $device->code1 }}<br>{{ $device->code2 }}<br>{{ $device->code3 }}</td>
                                        <td>
                                            <form action="{{ route('ss.delete',[$device->id]) }}" method="post">{{ csrf_field() }}<button type="submit" name="submit" value="Remove" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-remove"></span></button></form></td>
                                    </tr>
                                    @empty
                                    <tr><th colspan="6" class="text-center">No any registered devices</th></tr>
                                @endforelse
                                </tbody></table></div>
                    </div>
                </div>
            </div>
            <div class="col col-md-4"><form method="post">{{ csrf_field() }}
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Add Devices</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            @foreach($Details as $th => $td)
                            <div class="col-md-12">{!! formGroup(2,$td,'text',$th,old($td,'')) !!}</div>
                            @endforeach
                            @foreach(range(1,3) as $num)
                            <div class="col-md-12">{!! formGroup(2,"code{$num}",'text',"Code {$num}",old("code{$num}",'')) !!}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="panel-footer clearfix">
                        <input type="submit" name="submit" value="Add Device" class="btn btn-info pull-right">
                    </div>
                </div>
                </form></div>
        </div>
    </div>

@endsection