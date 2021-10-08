@extends("ebis.page")
@include('BladeFunctions')
@php $Data = Request()->id ? \App\Models\eBis::with('Subscriptions')->find(Request()->id) : null; @endphp
@section("content")
    <div class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <form method="post" enctype="multipart/form-data" class="">{{ csrf_field() }}
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Add New Client for eBis</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ebis.index'):url()->previous()) !!}</div>
                        <div class="panel-body">
                            <div class="row">
                                {!! formGroup(1,'product','hidden','',old('product',array_get($Data,'product'))) !!}
                                <div class="col-md-6">{!! formGroup(1,'customer','select','Customer',old('customer',array_get($Data,'customer')),[
                        'selectOptions' => \App\Models\CustomerRegistration::with(['Customer','Product','Edition'])->get()->map(function($reg){  return collect($reg)->only(['seqno','remarks'])->merge(['text' => $reg['Customer']['name'] . ($reg['remarks']?' ('.$reg['remarks'].')':''), 'value' => $reg['Customer']['code'], 'attr' => 'data-seq="'.$reg['seqno'].':'.implode(" ",[$reg['Product']['name'],$reg['Edition']['name'],'Edition']).'"']); })->toArray(),
                         'attr' => 'onchange=CustomerChanged(this)'
                        ]) !!}</div>
                                <div class="col-md-6">{!! formGroup(1,'seq','select','Product','',['attr' => 'readonly']) !!}</div>
                            </div>
                            <h3>Subscriptions</h3>
                            <div class="row">
                                <div class="col-md-3">{!! formGroup(1,'package','select','Package',old('package',array_get($Data,'package')),['selectOptions' => ['Basic']]) !!}</div>
                                <div class="col-md-3">{!! formGroup(1,'start','text','Start Date (yyyy-mm-dd)',old('start',array_get($Data,'start',date('Y-m-d'))),['attr' => 'placeholder="'.date('Y-m-d').'"']) !!}</div>
                                <div class="col-md-3">{!! formGroup(1,'end','text','End Date (yyyy-mm-dd)',old('end',array_get($Data,'end',date('Y-m-d',strtotime("+1 year")))),['attr' => 'placeholder="'.date('Y-m-d',strtotime("+1 year")).'"']) !!}</div>
                                <div class="col-md-3">{!! formGroup(1,'host','text','URL',old('domain',array_get($Data,'host')),['attr' => 'placeholder="example.org"']) !!}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">{!! formGroup(1,'database','text','Database',old('database',array_get($Data,'database'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'username','text','Username',old('username',array_get($Data,'username'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'password','text','Password',old('password',array_get($Data,'password'))) !!}</div>
                            </div>
                        </div>
                        <div class="panel-footer clearfix">
                            <input type="submit" name="submit" value="Submit" class="btn btn-info pull-right">
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
