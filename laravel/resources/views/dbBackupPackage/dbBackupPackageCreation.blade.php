@extends("layouts.app")
@section('title', 'MIT :: dbBackupPackage')
@include('BladeFunctions')
@section("content")


<div class="content">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8"><form method="post" action="products" onSubmit="return ValidateBaseName()">{{ csrf_field() }}
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Add new Package</strong><a href="products" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
                    <div class="panel-body">
                        <div class="product_create_form">
                            <div class="form-group clearfix">
                                <div class="col-xs-6">
                                    <label for="name">Package Code</label>
                                    <input type="text" class="form-control" id="name" name="code" required value="{{ old('code')?:((new \App\Models\Product)->NextCode()) }}">
                                </div>
                                <div class="col-xs-6">
                                    <label for="basename">Package Name</label>
                                    <input type="text" class="form-control" id="basename" name="basename" required value="{{ old('basename') }}">
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="col-xs-12">
                                    <label for="name">Display Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="form-group clearfix">
                                <div class="col-xs-12">
                                    <label for="description">Description</label>
                                    <textarea name="description_public" id="description" class="form-control">{{ old('description_public') }}</textarea>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="col-xs-12">
                                    <label for="description_internal">Description (for internal purpose)</label>
                                    <div class="checkbox" style="margin-top: 0px">
                                        <label for="same_as_public"><input id="same_as_public" type="checkbox"> Same as Public Description</label>
                                    </div>
                                    <textarea name="description_internal" id="description_internal" class="form-control">{{ old('description_internal') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer clearfix">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form></div>
        <div class="col-md-2"></div>
    </div>
</div>

@endsection