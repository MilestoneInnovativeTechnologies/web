@extends("ebis.page")
@include('BladeFunctions')
@php $Data = Request()->id ? \App\Models\eBis::with(['Customer','Subscriptions'])->find(Request()->id) : null; @endphp
@section("content")
    <div class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <form method="post" enctype="multipart/form-data" class="">{{ csrf_field() }}
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Add New eBis Subscription for {{ $Data->Customer->name }}</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ebis.index'):url()->previous()) !!}</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">{!! formGroup(1,'package','select','Package',old('package',array_get($Data,'package')),['selectOptions' => ['Basic']]) !!}</div>
                                <div class="col-md-3">{!! formGroup(1,'start','text','Start Date (yyyy-mm-dd)',old('start',array_get($Data,'start',date('Y-m-d'))),['attr' => 'placeholder="'.date('Y-m-d').'"']) !!}</div>
                                <div class="col-md-3">{!! formGroup(1,'end','text','End Date (yyyy-mm-dd)',old('end',array_get($Data,'end',date('Y-m-d',strtotime("+1 year")))),['attr' => 'placeholder="'.date('Y-m-d',strtotime("+1 year")).'"']) !!}</div>
                                <div class="col-md-3">{!! formGroup(1,'host','text','URL',old('host',array_get($Data,'host')),['attr' => 'placeholder="example.org"']) !!}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">{!! formGroup(1,'database','text','Database',old('database',array_get($Data,'database'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'username','text','Username',old('username',array_get($Data,'username'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'password','text','Password',old('password',array_get($Data,'password'))) !!}</div>
                            </div>
                            <h3>Current Subscriptions</h3>
                            <div class="table-responsive"><table class="table table-striped"><thead><tr><th>No</th><th>Package</th><th>Host</th><th>Start Date</th><th>End Date</th><th>Status</th></tr></thead><tbody>
                                    @foreach($Data->Subscriptions as $subscription)
                                        <tr><td>{{ $loop->iteration }}</td>
                                            @foreach(['package','host','start','end','status'] as $Field)
                                                <td>{{ $subscription->$Field }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody></table></div>
                        </div>
                        <div class="panel-footer clearfix">
                            {!! PanelFooterButton('Add Subscription','success') !!}
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@push('js')
    <script type="text/javascript">
        $(function(){ $('[name="customer"]').trigger('change') });
        function CustomerChanged(ele){
            seq = ele.options[ele.selectedIndex].getAttribute('data-seq');
            $('[name="seq"]').html(new Option(seq.split(":")[1],seq.split(":")[0]));
            $('[name="product"]').val(seq.split(":")[1]);
        }
    </script>
@endpush
