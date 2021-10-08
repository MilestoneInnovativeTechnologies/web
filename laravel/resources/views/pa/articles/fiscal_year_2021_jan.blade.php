@extends("pa.articles.layout")
@section('content')

<div class="page_contents">
	<div class="page pa" style="background-color: #EBEBEB">
		<div class="container">


			<br><br><br><br><br><br><br>
			<h1><b><u><center>Adding New Fiscal Year - 2021</center></u></b></h1>
			<p style="text-align: center; font-weight: bold"> A fiscal year is a one-year period that companies and governments use for financial reporting and budgeting. A fiscal year is most commonly used for accounting purposes to prepare financial statements.</p>
			<h2><u><center>How to add new fiscal year on ePlus</center></u></h2>
			<p align="center">
				<iframe width="783" height="442" src="https://www.youtube.com/embed/fS4bMkp9830" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</p>
			<p align="center">
				<strong><a href="https://youtu.be/fS4bMkp9830" target="_blank">Watch on Youtube: https://youtu.be/fS4bMkp9830</a></strong>
			</p>
			<br><br>
			<h3><b><u><center>Step By Step Procedure</center></u></b></h3>
			<h5 align="left" style="margin-top: 40px"><b><u>Step 1:</u></b></h5>
			<p align="left">Go to Tools >Settings > Fiscal year</p>
			<img src="https://github.com/MilestoneInnovativeTechnologies/web/blob/43c725f0f9a17c994ae36403408553edb2a02d44/laravel/resources/views/pa/articles/files/step%201.jpeg?raw=true" alt="Step 1">
			<h5 align="left" style="margin-top: 40px"><b><u>Step 2:</u></b></h5>
			<p align="left"><b>Select new option</b></p>
			<img src="https://github.com/MilestoneInnovativeTechnologies/web/blob/43c725f0f9a17c994ae36403408553edb2a02d44/laravel/resources/views/pa/articles/files/step%202.jpeg?raw=true" alt="Step 2">
			<h5 align="left" style="margin-top: 40px"><b><u>Step 3:</u></b></h5>
			<p align="left"><b>Enter the new fiscal year details as shown below and then save it.</b></p>
			<img src="https://github.com/MilestoneInnovativeTechnologies/web/blob/43c725f0f9a17c994ae36403408553edb2a02d44/laravel/resources/views/pa/articles/files/step%203.jpeg?raw=true" alt="Step 3">
			<h5 align="left" style="margin-top: 40px"><b><u>Step 4:</u></b></h5>
			<p align="left"><b>Reopen the software.. Now while opening any window on software, a browse window with all fiscal year appears as shown below. You can choose the required year.</b></p>
			<img src="https://github.com/MilestoneInnovativeTechnologies/web/blob/43c725f0f9a17c994ae36403408553edb2a02d44/laravel/resources/views/pa/articles/files/step%204.jpeg?raw=true" alt="Step 6">

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