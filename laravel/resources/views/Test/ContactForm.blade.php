@extends("layouts.app")
@section('title', 'MIT :: Test')
@include('BladeFunctions')
@section("content")
<form id="product-form" action="{{url('/contact/store')}}" method="POST">

    {{ csrf_field() }}

    <div class="form-group">
        <label for="product">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Full Name">
    </div>

    <div class="form-group">
        <label for="product">Email</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email">
    </div>

    <div class="form-group">
        <label for="product">Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter Subject">
    </div>

    <div class="form-group">
        <label for="product">Message</label>
        <textarea class="form-control" rows="5" id="message" name="message" name="message"></textarea>
    </div>

    <div class="form-group">
        <input class="btn btn-lg btn-primary" id="submit" type="submit" value="Save" name="submit" />
    </div>

</form>