<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\VacancyApplicants as Applicants;
use Illuminate\Http\Request;


class VacancyController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request,['title'=>'required']);
        $Vacancy = new Vacancy;
        foreach($request->only(['code','title','description','date']) as $name => $value)
            $Vacancy->{$name} = $value;
        $Vacancy->save();
        if($request->spec && !empty($request->spec)){
            $Specs = [];
            foreach($request->spec as $spec)
                $Specs[] = new \App\Models\VacancySpecification($spec);
            $Vacancy->spec()->saveMany($Specs);
        }
        $Vacancy->push();
        return redirect()->route('vacancy.manage')->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Vacancy posted successfully.']);
    }

    public function PreVacancyNotify(Request $request){
        \Storage::disk('www')->append('VacancyPreNotifyEmails.db', implode("\t",[date('d/m/y'),$request->get("jv_notify_email")]));
        return back()->with(['email' => "Your Email address have been added to our list. Thank you for showing interest in us."]);
    }

    public function apply(Vacancy $Vacancy, Request $request){
        $this->validate($request,[ 'name'  => 'required', 'phone'  => 'required', 'email'  => 'required', 'resume'  => 'required' ]);
        $resume = $request->file('resume')->store('', 'resumes');
        $Applicant = new Applicants;
        foreach($request->only(['name','phone','email','message']) as $name => $value) $Applicant->{$name} = $value;
        $Applicant->resume = $resume;
        $Vacancy->applicants()->save($Applicant);
        \App\Libraries\SMS::init(new \App\Sms\JobApplicationSuccess($Applicant))->sendTo($request->get('phone'));
        return back()->with(['info' => true, 'applicant' => $Applicant]);
    }

    public function download(Applicants $Applicant){
        return response()->download(\Storage::disk('resumes')->getDriver()->getAdapter()->applyPathPrefix($Applicant->resume),implode(".",[$Applicant->Vacancy->title,$Applicant->name,substr(strstr($Applicant->resume,"."),1)]));
    }

    public function on(Vacancy $code){
        $code->live = 1; $code->save(); return back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	$code->title . ' is LIVE now.']);
    }
    public function off(Vacancy $code){
        $code->live = "0"; $code->save(); return back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	$code->title . ' is OFFLINE now.']);
    }
}
