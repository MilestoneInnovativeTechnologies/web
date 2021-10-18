<form method="post" enctype="multipart/form-data" action="{!! route('sk.edition.add') !!}" class="">{{ csrf_field() }}
    <div class="panel panel-default">
        <div class="panel-heading">Add Edition</div>
        <div class="panel-body">
            {!! formGroup(2,'name','text','Name',old('name','')) !!}
            {!! formGroup(2,'detail','textarea','Details',old('detail','')) !!}
        </div>
        <div class="panel-footer text-right">
            <input type="submit" name="submit" value="Add Edition" class="btn btn-primary">
        </div>
    </div>
</form>
