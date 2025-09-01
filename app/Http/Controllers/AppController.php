<?php

namespace App\Http\Controllers;

use App\Enums\DoctorStatusEnum;
use App\Models\User;
use App\Models\Activity;
use App\Models\Census;
use App\Models\Doctor;
use App\Models\Incidence;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        $activities = Activity::with('typeActivity')
            ->with('user')
            ->orderBy('id', 'desc')
            ->limit(7)
            ->get();



        $usersCount = User::count();
        $doctorsCount = Doctor::count();
        $doctorsCount = Doctor::count();
        $doctorsInCourse = Doctor::where('status', DoctorStatusEnum::IN_COURSE->value)->count();
        $doctorsWithIncidence = Doctor::where('status', DoctorStatusEnum::WITH_INCIDENCE->value)->count();
        $doctorsWithSanction = Doctor::where('status', DoctorStatusEnum::SANCTIONED->value)->count();
        $censusesCount = Census::count();
        $incidencesCount = Incidence::count();

        return view('dashboard.index')->with(compact(
            'activities',
            'usersCount',
            'doctorsCount',
            'doctorsInCourse',
            'doctorsWithIncidence',
            'doctorsWithSanction',
            'censusesCount',
            'incidencesCount',
        ));
    }
}
