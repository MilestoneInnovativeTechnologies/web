@extends("vacancy.page")
@include('BladeFunctions')
@section("content")
@php
$Vacancy = \App\Models\Vacancy::with('Spec','Applicants')->find($code);
//dd($Vacancy->toArray())
@endphp
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading"><strong>{{ $Vacancy->title }}</strong>{!! PanelHeadBackButton(Route('vacancy.manage')) !!}</div>
            <div class="panel-body">
                <p>{!! nl2br($Vacancy->description) !!}</p>
                <div class="table-responsive"><table class="table table-condensed"><caption>Specifications</caption><tbody>
                        @forelse($Vacancy->Spec as $spec)
                            <tr><th>{{ $spec->title }}</th><th>:</th><td>{!! nl2br($spec->detail) !!}</td></tr>
                            @empty
                            <tr><th>No Any Specification</th></tr>
                        @endforelse
                        </tbody></table></div>
                <div class="table-responsive"><table class="table table-condensed"><caption>Applicants</caption>
                        <thead><tr><th>No</th><th>Name</th><th>Date</th><th>Contact</th><th>Message</th><th>Resume</th></tr></thead>
                        <tbody>
                        @forelse($Vacancy->Applicants as $Applicant)
                            <tr><th nowrap>{{ $loop->iteration }}</th><th nowrap>{{ $Applicant->name }}</th><th>{{ date('D d/M/y, h:i A',strtotime($Applicant->created_at)) }}</th>
                                <td nowrap>
                                    <b>Phone: </b>{{ $Applicant->phone }}
                                    <br><b>Email: </b>{{ $Applicant->email }}
                                </td>
                                <td>{!! nl2br($Applicant->message) !!}</td>
                                <td nowrap><a href="{{ route('vacancy.resume.download',$Applicant->id) }}">Download</a></td>
                            </tr>
                            @empty
                            <tr><th colspan="4">No Any Applications Yet</th></tr>
                        @endforelse
                        </tbody></table></div>
            </div>
        </div>
    </div>
@endsection