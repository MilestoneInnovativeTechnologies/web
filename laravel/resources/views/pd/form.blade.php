@extends("pd.page")
@include('BladeFunctions')
@php $Data = Request()->id ? \App\Models\PD::with('Tables')->find(Request()->id) : null; @endphp
@section("content")
    <div class="content">
        <form method="post" class="">{{ csrf_field() }}
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Add New Product Demonstration Details</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('pd.index'):url()->previous()) !!}</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">{!! formGroup(2,'customer','select','Customer',old('customer',array_get($Data,'customer')),[
                        'selectOptions' => \App\Models\CustomerRegistration::with(['Customer','Product','Edition'])->get()->map(function($reg){  return collect($reg)->only(['seqno','remarks'])->merge(['text' => $reg['Customer']['name'] . ($reg['remarks']?' ('.$reg['remarks'].')':''), 'value' => $reg['Customer']['code'], 'attr' => 'data-seq="'.$reg['seqno'].':'.implode(" ",[$reg['Product']['name'],$reg['Edition']['name'],'Edition']).'"']); })->toArray(),
                         'attr' => 'onchange=CustomerChanged(this)'
                        ]) !!}</div>
                        <div class="col-md-6">{!! formGroup(2,'seq','select','Product','',['attr' => 'readonly']) !!}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">{!! formGroup(1,'url_web','text','Website URL',old('url_web',array_get($Data,'url_web'))) !!}</div>
                        <div class="col-md-4">{!! formGroup(1,'url_api','text','API URL',old('url_api',array_get($Data,'url_api'))) !!}</div>
                        <div class="col-md-4">{!! formGroup(1,'url_interact','text','Interact URL',old('url_interact',array_get($Data,'url_interact'))) !!}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">{!! formGroup(1,'date_start','text','Service Start Date',old('date_start',array_get($Data,'date_start') ?: date('Y-m-d'))) !!}</div>
                        <div class="col-md-6">{!! formGroup(1,'date_end','text','Service End Date',old('date_end',array_get($Data,'date_end') ?: date('Y-m-d',strtotime("+1 year")))) !!}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Tables</h3>
                            <table class="table table-hover tables">
                                <thead><tr><th>Table Name</th><th>Latest Created Date</th><th>Latest Updated Date</th><th>Actions</th></tr></thead>
                                <tbody>@php $tables = old('table'); @endphp
                                @if($tables)
                                @foreach($tables as $name => $value)
                                    <tr id="{{ $name }}"><td>{!! formGroup(1,'table['.$name.']','text','',$value) !!}</td><td>{!! formGroup(1,'last_created['.$name.']','text','',old('last_created')[$name]) !!}</td><td>{!! formGroup(1,'last_updated['.$name.']','text','',old('last_updated')[$name]) !!}</td><td><a href="javascript:DeleteTableLine({{ $name }})" class="btn btn-default"><span class="glyphicon glyphicon-minus"></span></a></td></tr>
                                @endforeach
                                @elseif($Data && $Data->Tables && !empty( $Data->Tables ))
                                    @foreach($Data->Tables as $K => $TBL)
                                        <tr id="{{ $K }}"><td>{!! formGroup(1,'table['.$K.']','text','',$TBL->table) !!}</td><td>{!! formGroup(1,'last_created['.$K.']','text','',$TBL->last_created) !!}</td><td>{!! formGroup(1,'last_updated['.$K.']','text','',$TBL->last_updated) !!}</td><td><a href="javascript:DeleteTableLine({{ $K }})" class="btn btn-default"><span class="glyphicon glyphicon-minus"></span></a></td></tr>
                                    @endforeach
                                @else
                                    <tr><td>{!! formGroup(1,'table['.time().']','text','') !!}</td><td>{!! formGroup(1,'last_created['.time().']','text','') !!}</td><td>{!! formGroup(1,'last_updated['.time().']','text','') !!}</td></tr>
                                @endif
                                </tbody>
                                <tfoot><tr><td colspan="4"><a href="javascript:AddTableLine()" class="btn btn-default btn-sm pull-left"><span class="glyphicon glyphicon-plus"></span> &nbsp; Add More Table</a> </td></tr></tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="panel-footer clearfix">
                    <input type="submit" name="submit" value="Add PD" class="btn btn-info pull-right">
                </div>
            </div>
        </form>
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