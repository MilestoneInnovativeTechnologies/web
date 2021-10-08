@extends("pa.articles.layout")
@section('content')

<div class="page_contents">
	<div class="page pa" style="background-color: #EBEBEB">
		<div class="container">

			<br><br><br><br><br><br><br>
			<h1><b><u>
						<center>Steps for deactivating CESS</center>
					</u></b></h1>
			<p style="text-align: center; font-weight: bold">

			</p>

			<h4 align="left">
				The Kerala Government have discontinued System of cess. There are some changes in ePlus software for deactivating existing cess. Here are the steps for cess deactivation.
			</h4>
			<br>
			<h2 align="left">• &nbsp;  Open the ePlus software.</h2>
			<h2 align="left">• &nbsp;  Tools -> Settings -> Function Settings</h2>
			<img src="https://github.com/MilestoneInnovativeTechnologies/web/raw/ac8ee0e2d0e5644e7b8362fc5424fbc9d9bfeb20/laravel/resources/views/pa/articles/files/DEACTIVATE_CESS_01.png"			 alt="image001">
			<h3 align="left"><b>• &nbsp;  Click on sales (from list in left side of window) <br>•	Right click on Sales B2C -> Edit.</b></h3>
			<img src="https://github.com/MilestoneInnovativeTechnologies/web/raw/ac8ee0e2d0e5644e7b8362fc5424fbc9d9bfeb20/laravel/resources/views/pa/articles/files/DEACTIVATE_CESS_02.png"
				 alt="image002">
			<p align="left"><b>• &nbsp;	A new window will be open.<br>• &nbsp;	Select ‘Tax’ tab.<br>• &nbsp;	Select “Any” as User Type and save it.<br>
				</b></p>
			<img src="https://github.com/MilestoneInnovativeTechnologies/web/raw/ac8ee0e2d0e5644e7b8362fc5424fbc9d9bfeb20/laravel/resources/views/pa/articles/files/DEACTIVATE_CESS_03.png"
				 alt="image003">
			<h3 align="center"><b>OR</b></h3>
			<p align="left"><b>If user type is not defined in ‘Tax’ window then follow the instruction given below.</b></p>
			<p align="left"><b>&nbsp;	•	Select ‘Tax01’ as Tax Rule -> Save</b></p><br><br><br>
					<img src="https://github.com/MilestoneInnovativeTechnologies/web/raw/ac8ee0e2d0e5644e7b8362fc5424fbc9d9bfeb20/laravel/resources/views/pa/articles/files/DEACTIVATE_CESS_04.png" alt="image004">
			<p align="left"><b>•	Close the window</b></p><br><br><br>
					<br><br><br>

		</div>
	</div>@include("home.section_contact_wo_map")</div>
		@include("home.login_modal")

@endsection
@push('js')
<script type="text/javascript">
function login(){
	$('#loginModal').modal('show');
}
</script>

@endpush