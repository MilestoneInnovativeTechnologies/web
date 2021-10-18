<div class="panel panel-default">
    <div class="panel-heading"><strong>Add Client</strong></div>
    <div class="panel-body">
        <form method="post" enctype="multipart/form-data" action="{!! route('sk.add') !!}" class="">{{ csrf_field() }}
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4">{!! formGroup(1,'partner','select','Partner',old('name',array_get($Data,'name')),['selectOptions' => \App\Models\Customer::where('status','<>','INACTIVE')->orderBy('name')->pluck('name','code')->toArray()]) !!}</div>
                <div class="col-xs-12 col-sm-12 col-md-4">{!! formGroup(1,'name','text','Name',old('name',array_get($Data,'name')),['attr' => 'placeholder="Client Name"']) !!}</div>
                <div class="col-xs-12 col-sm-12 col-md-2">{!! formGroup(1,'code','text','Code',old('name',array_get($Data,'code')),['attr' => 'placeholder="Client Code"']) !!}</div>
                <div class="col-xs-12 col-sm-12 col-md-2"><div class="form-group clearfix"><label class="control-label " style="">&nbsp;</label><input type="submit" name="submit" value="Add New Client" class="btn btn-info btn-block"></div></div>
            </div>
        </form>
    </div>
</div>
