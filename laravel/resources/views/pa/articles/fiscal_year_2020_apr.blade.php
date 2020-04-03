@extends("pa.articles.layout")
@section('content')

<div class="page_contents">
	<div class="page pa" style="background-color: #EBEBEB">
		<div class="container">

			<br><br><br><br><br><br><br>
			<h1><b><u>
						<center>Fiscal Year</center>
					</u></b></h1>
			<p style="text-align: center; font-weight: bold">
				A fiscal year is a one-year period that companies and governments use for financial reporting and
				budgeting. A fiscal year is most commonly used for accounting purposes to prepare financial
				statements.
			</p>

			<h2><u>
					<center>How to add new fiscal year on software (2020 - 2021)</center>
				</u></h2>
			<h3 align="left"><b><u>Step 1:</u></b></h3>
			<h2 align="left"> >> Open the ePlus software.</h2>
			<h2 align="left"> >> In Tools menu select the option fiscal year.</h2>
			<p align="left"><b>Go to Tools >Settings > Fiscal year</b></p>
			<img src="https://raw.githubusercontent.com/MilestoneInnovativeTechnologies/web/master/laravel/resources/views/pa/articles/files/IMG001.png"
				 alt="image001">
			<h3 align="left"><b><u>Step 2:</u></b></h3>
			<p align="left"><b>Select new option. And enter the new fiscal year details as shown below.</b></p>
			<img src="https://raw.githubusercontent.com/MilestoneInnovativeTechnologies/web/master/laravel/resources/views/pa/articles/files/IMG002.png"
				 alt="image002">
			<h3 align="left"><b><u>Step 3:</u></b></h3>
			<p align="left"><b>Save and close the application.</b></p>
			<h3 align="left"><b><u>Step 4:</u></b></h3>
			<p align="left"><b>Reopen the software.</b></p>
			<h3 align="left"><b><u>Step 5:</u></b></h3>
			<p align="left"><b>Now while opening any window on software, a browse window with all fiscal year appears as
					shown below. You can choose the required year.</b></p>
			<img src="https://raw.githubusercontent.com/MilestoneInnovativeTechnologies/web/master/laravel/resources/views/pa/articles/files/IMG003.png"
				 alt="image003"><br><br><br>

		</div>
	</div>@include("home.section_contact_wo_map")</div>
	
	<div id="loginModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content"><form method="post" action="{{ Route('login') }}" class="form-horizontal">{{ csrf_field() }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Login</h4>
				</div>
				<div class="modal-body">
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label class="control-label col-xs-4">Email Address:</label>
							<div class="col col-xs-8">
								<input type="text" name="email" value="" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label class="control-label col-xs-4">Password:</label>
							<div class="col-xs-8">
								<input type="password" name="password" value="" class="form-control">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<input type="submit" class="btn btn-primary" value="Login">
				</div>
			</form></div>
		</div>
	</div>
@endsection
@push('js')
<script type="text/javascript">
function login(){
	$('#loginModal').modal('show');
}
</script>

@endpush
