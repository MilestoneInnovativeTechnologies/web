@extends("pa.articles.layout")
@section('content')

<div class="page_contents">
	<div class="page pa" style="background-color: #EBEBEB">
		<div class="container">


			<br><br><br><br><br><br><br>
			<h1><b><u><center>Adding New Fiscal Year - 2022</center></u></b></h1>
			<p style="text-align: center; font-weight: bold"> A fiscal year is a one-year period that companies and governments use for financial reporting and budgeting. A fiscal year is most commonly used for accounting purposes to prepare financial statements.</p>
			<h2><u><center>How to add new fiscal year on ePlus</center></u></h2>
<!--			<p align="center">
				<iframe width="783" height="442" src="https://www.youtube.com/embed/fS4bMkp9830" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</p>
			<p align="center">
				<strong><a href="https://youtu.be/fS4bMkp9830" target="_blank">Watch on Youtube: https://youtu.be/fS4bMkp9830</a></strong>
			</p>-->
			<br><br>
			<h3><b><u><center>Step By Step Procedure</center></u></b></h3>
			<h5 align="left" style="margin-top: 40px"><b><u>Step 1:</u></b></h5>
			<p align="left">Open the ePlus software and from <strong>Tools</strong> menu select the option <strong>fiscal year</strong>.<br />Go to Tools<strong> > </strong>Settings<strong> > </strong>Fiscal year</p>
			<img src="https://i.ibb.co/1Z1zJ6L/image-2021-12-29-170355.png" alt="Step 1">
			<h5 align="left" style="margin-top: 40px"><b><u>Step 2:</u></b></h5>
			<p align="left"><b>Select <strong>New</strong> option</b></p>
			<img src="https://i.ibb.co/VNZLzSV/image-2021-12-29-170712.png" alt="Step 2">
			<h5 align="left" style="margin-top: 40px"><b><u>Step 3:</u></b></h5>
			<p align="left"><b>Enter the new fiscal year details as shown below. And the click on <strong>Save</strong> button and close the application and open it again.</b></p>
			<img src="https://i.ibb.co/7R13wfM/image-2021-12-29-170836.png" alt="Step 3">
			<h5 align="left" style="margin-top: 40px"><b><u>Step 4:</u></b></h5>
			<p align="left"><b>Now while opening any window on software, a browse window with all fiscal year appears as shown below. You can choose the required year.</b></p>
			<img src="https://i.ibb.co/9VVXmQ4/image-2021-12-29-170937.png" alt="Step 4">

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