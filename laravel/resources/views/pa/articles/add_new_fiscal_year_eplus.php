@extends("pa.articles.layout")
@section('content')

<div class="page_contents">
<div class="page pa" style="background-color: #EBEBEB">
<div class="container">
<br><br><br><br><br><br><br><h1><b><u><center>Fiscal Year</center></u></b></h1>
<p><center><b>It helps to record the business data in year-wise. It provides options to adjust duration of a fiscal year.</b></cener></p>
<h2><u><center>How to add new fiscal year on software (2018-2019)</center></u></h2>
<h3 align="left"><b><u>Step 1:</u></b></h3>
<p align="left"><b>Go to Tools >Settings > Fiscal year</b></p>
<image src="https://lh3.googleusercontent.com/C-qyJ3cNFJ_i4KDLaQ5UbFsDBZhfBZgNE6nDLrnxRSS11wu8HP_-4igjiD8q0FFZxjB5lJyhrtQ6ZyD-32QcLENsct-Qqzk2Wi8730DYJ3oru3pxuuWJc64ZNTQpfysCKnygrU4=w1142-h511-no" alt="aa">
<h3 align="left"><b><u>Step 2:</u></b></h3>
<p align="left"><b>Select new option. And enter the new fiscal year details as shown below.</b></p>
<image src="https://lh3.googleusercontent.com/IMHdPqS4OZ2LbYSylPGEfMBlizsA7dbjsK9lANAgLywvy7kHDNG8dnCB2H2fYkElrxarrSwqSWJZGbcM69WusVWJ0yTxEeo8YKwhfDTjtL_Q1rzi6I-srde1bhSHMUbLEPIK9m0HjN-Wds5bf_KQvPg4iPvnim-KrT1xwSYAxRqZ36vA_jXrW1PRi5ANbLprsWMrtl_rVFMMAOdPkHAu9afU8phScdbwHGpZRVqpTXgNEzuojPPdvnW-u7w4SOjnWylX07a4VfHorUz6Ou2qD6tsPMuybYvXE8TYRjWQeftLAxAyof_YxN1rzwoXz66kKtlw_Jt3_ENid0Kv9SHNhPsavz17qU0FGtQFub0wbKVjboXrl_HjB_yJpXmH1k7--sfeLAuSwcBaZJ98o5TABCA72iKQmiGvjmWYb7_z9yiodshH0baoUCp31H3aTD4kZD8JjmlBAhQF6W8IeRcxvmqLUqwW6FtwiitNWePjd5_M2hpCbLnZAcAxgpyvcqCWtTmrNGQfrafEYZrag0mQz2AmRIFwgEvtKDXOYM7CjHk_4TEgnIXKtcpkkaVbGXPmeSKAZ9Y3cynWssfCmmddXOWBUIoYfOY=w670-h507-no" alt="bb">
<h3 align="left"><b><u>Step 3:</u></b></h3>
<p align="left"><b>Save and close the application.</b></p>
<h3 align="left"><b><u>Step 4:</u></b></h3>
<p align="left"><b>Reopen the software.</b></p>
<h3 align="left"><b><u>Step 5:</u></b></h3>
<p align="left"><b>Now while opening any window on software, a browse window with all fiscal year appears as shown below. You can choose the required year.</b></p>
<image src="https://lh3.googleusercontent.com/JCGulJhx0SVtsBZoXZVkwjnpu9r6ud0nUJZcJGWjFpex83GC-uDTgO4PyEaqr1CCgg35m8SKRhWnEvLs0bqR1lgkCWUpbnHAzES3Jr41uEjuZVdfXYK1HLH91kj63NnewOEzByV5L8vp1iA-LSTPgJSxhYOPTn2M6lOqkvXQ0a37yhHtXOCWA2fEM9uhXaPS4TcMehEm5ONpt7-aOdPsneXqLhhuILpKtNn5N7Q09tWXEkMFn4g5QhcMEk4Io9l-YnKSZi_LwgIOv4KUKQY9QXKYqNqm6xoTvL9YeUDtozDrKiPcSTBMS8z5dWkkNtivjaLZz9Xe_8uU5vNQZXiTSr8OurBywB0Xb-wDcUiknv8eZn0-79PFiqUoOyJBawEj9lpo5R6xazFwOK5Kw1X85Oz1GLEPbX0r7ubY5-zNjTFKlkM06QC5T-ClMdO0o4NwD_TUkyAIh_J2C6ig9fx1xDaa-Jh1qbJqAbuWtUS4Bmmuj5snMbOMzA_qM8-ioK5B7NrfgpMNdjaQHmGAlEmeaxyKadmtF5nslYllqevC3SvYflYXbcBg3EjFelAYQEEizBX7nF6Ye1UWI2fRLMsW5tmwuODbLB6Gzw7tqHFrPUf7ekMnYiTwta0T-Z0ciePmCAsFXVHeoN_n1XeyxGY6RAS4=w1118-h537-no" alt="cc">

</div></div>






		@include("home.section_contact_wo_map")
	</div>
	
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