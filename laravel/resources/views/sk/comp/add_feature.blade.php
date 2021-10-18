<form method="post" enctype="multipart/form-data" action="{!! route('sk.feature.add') !!}" class="">{{ csrf_field() }}
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Add Feature</strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    {!! formGroup(1,'code','text','Code',old('code',''),['attr' => 'placeholder="Unique code"']) !!}
                    <a class="small" onClick="getFeatureCode()" style="position: absolute; top:5px; right:20px; cursor: pointer">Generate New</a>
                </div>
                <div class="col-md-4">{!! formGroup(1,'type','select','Type',old('type',''),['selectOptions' => ['Yes/No','Detail']]) !!}</div>
                <div class="col-md-4">{!! formGroup(1,'default','text','Default',old('code',''),['attr' => 'placeholder="Default Value"']) !!}</div>
            </div>
            <div class="row">
                {!! formGroup(1,'level','hidden',null,old('level','0')) !!}
                <div class="col-md-6">
                    {!! formGroup(1,'name','text','Name',old('code',''),['attr' => 'placeholder="Feature Name"']) !!}
                    {!! formGroup(1,'parent','select','Parent',old('code',''),['selectOptions' => parentFeatures(),'attr' => 'onChange="parentChanged(this)"']) !!}
                </div>
                <div class="col-md-6">{!! formGroup(1,'detail','textarea','Description',old('detail',''),['attr' => 'rows="5"']) !!}</div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" name="submit" value="Add Feature" class="btn btn-primary">
        </div>
    </div>
</form>
@push('js')
    <script>
        function getFeatureCode(){
            inp = $('input[name="code"]'); inp.val('');
            $.get('{!! route('sk.feature.code') !!}',function(r){
                inp.val(r)
            })
        }
        function parentChanged(e){
            inp = $('input[name="level"]'); inp.val('');
            inp.val(parseInt(e.selectedOptions[0].getAttribute('level'))+1)
        }
    </script>
@endpush