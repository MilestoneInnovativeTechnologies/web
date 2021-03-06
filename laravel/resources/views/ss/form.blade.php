@extends("ss.page")
@include('BladeFunctions')
@php $Data = Request()->id ? \App\Models\SmartSale::with('Tables')->find(Request()->id) : null; @endphp
@section("content")
    <div class="content">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <form method="post" enctype="multipart/form-data" class="">{{ csrf_field() }}
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Add New Smart Sale Client</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('ss.index'):url()->previous()) !!}</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">{!! formGroup(1,'customer','select','Customer',old('customer',array_get($Data,'customer')),[
                        'selectOptions' => \App\Models\CustomerRegistration::with(['Customer','Product','Edition'])->get()->map(function($reg){  return collect($reg)->only(['seqno','remarks'])->merge(['text' => $reg['Customer']['name'] . ($reg['remarks']?' ('.$reg['remarks'].')':''), 'value' => $reg['Customer']['code'], 'attr' => 'data-seq="'.$reg['seqno'].':'.implode(" ",[$reg['Product']['name'],$reg['Edition']['name'],'Edition']).'"']); })->toArray(),
                         'attr' => 'onchange=CustomerChanged(this)'
                        ]) !!}</div>
                                <div class="col-md-6">{!! formGroup(1,'seq','select','Product','',['attr' => 'readonly']) !!}</div>
                            </div><div class="row">
                                <div class="col-md-6">{!! formGroup(1,'name','text','Name to be displayed in App',old('name',array_get($Data,'name'))) !!}</div>
                                <div class="col-md-6">{!! formGroup(1,'image','file','Logo Image for App','') !!}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">{!! formGroup(1,'brief','textarea','Brief to be displayed in App',old('brief',array_get($Data,'brief'))) !!}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">{!! formGroup(1,'print_line1','text','Print Line 1',old('print_line1',array_get($Data,'print_line1'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'print_line2','text','Print Line 2',old('print_line2',array_get($Data,'print_line2'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'print_line3','text','Print Line 3',old('print_line3',array_get($Data,'print_line3'))) !!}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">{!! formGroup(1,'date_start','text','Service Start Date',old('date_start',array_get($Data,'date_start',date('Y-m-d')))) !!}</div>
                                <div class="col-md-6">{!! formGroup(1,'date_end','text','Service End Date',old('date_end',array_get($Data,'date_end',date('Y-m-d',strtotime("+1 year"))))) !!}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">{!! formGroup(1,'url_web','text','Website URL',old('url_web',array_get($Data,'url_web'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'url_api','text','API URL',old('url_api',array_get($Data,'url_api'))) !!}</div>
                                <div class="col-md-4">{!! formGroup(1,'url_interact','text','Interact URL',old('url_interact',array_get($Data,'url_interact'))) !!}</div>
                            </div>
                            <div class="row">
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Tables</h3>
                                    <table class="table table-hover tables table-condensed">
                                        <thead><tr><th>Table Name</th><th>Type</th><th>Delay</th><th>Condition</th><th>Last Sync Date Time</th><th>Last record create datetime</th></tr></thead>
                                        <tbody>@php
                                            $Tables = \App\Http\Controllers\SmartSaleController::$Tables;
                                            $Fields = \App\Http\Controllers\SmartSaleController::$Table_Fields;
                                            $DELAY = \App\Http\Controllers\SmartSaleController::$Table_DELAY;
                                            $CONDITION = \App\Http\Controllers\SmartSaleController::$Table_CONDITION;
                                            $DBTables = $Data ? $Data->Tables->keyBy('table')->toArray() : [];
                                        @endphp
                                        @foreach($Tables as $Table)
                                            <tr>
                                                <th>{{ $Table }}</th>
                                                <td>{!! formGroup(2,$Table . '[type]','select','',old($Table . '[type]',array_get($DBTables,$Table.'.type',$DELAY[$Table][0])),['selectOptions' => ['up','down','both'],'labelWidth'=>1]) !!}</td>
                                                <td>{!! formGroup(2,$Table . '[delay]','text','',old($Table . '[delay]',array_get($DBTables,$Table.'.delay',$DELAY[$Table][1])),['labelWidth'=>1]) !!}</td>
                                                <td>{!! formGroup(2,$Table . '[condition]','textarea','',old($Table . '[condition]',array_get($DBTables,$Table.'.condition',array_get($CONDITION,$Table,''))),['labelWidth'=>1]) !!}</td>
                                                <td>{!! formGroup(2,$Table . '[sync]','text','',old($Table . '[sync]',array_get($DBTables,$Table.'.sync')),['labelWidth'=>1]) !!}</td>
                                                <td>{!! formGroup(2,$Table . '[record]','text','',old($Table . '[record]',array_get($DBTables,$Table.'.record')),['labelWidth'=>1]) !!}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
            $('[name="seq"]').html(new Option(seq.split(":")[1],seq.split(":")[0]))
        }
        function AddTableLine() {
            code = new Date().getTime();
            $(`<tr id="${code}">`).html([
                $('<td>').html($('<input>').attr({ name:`table[${code}]`,type:'text',class:'form-control' })),
                $('<td>').html($('<input>').attr({ name:`last_created[${code}]`,type:'text',class:'form-control' })),
                $('<td>').html($('<input>').attr({ name:`last_updated[${code}]`,type:'text',class:'form-control' })),
                $('<td>').html($('<a>').attr({ href:`javascript:DeleteTableLine(${code})`,class:'btn btn-default' }).html($('<span>').attr({ class:'glyphicon glyphicon-minus' })))
            ]).appendTo($('.tables tbody'))
        }
        function DeleteTableLine(code) {
            $(`tr#${code}`).remove();
        }
    </script>
@endpush
@push('css')
    <style>
        .col-xs-11 { padding-left: 0px !important; padding-right: 0px !important; }
    </style>
@endpush