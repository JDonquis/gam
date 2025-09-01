<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Courses;
use App\Models\Sanction;
use App\Enums\DoctorStatusEnum;
use App\Models\DoctorIncidence;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckStatusDoctor implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $doctors = Doctor::where('status', DoctorStatusEnum::WITH_INCIDENCE->value)->get();

        foreach ($doctors as $doctor) {
            $incidence = DoctorIncidence::where('doctor_id', $doctor->id)->first();

            if (!isset($incidence->id)) {

                $course = Courses::where('doctor_id', $doctor->id)->first();

                $now = Carbon::now();
                $newStatus = $now->gt($course->end_date)
                    ? DoctorStatusEnum::AVAILABLE->value
                    : DoctorStatusEnum::IN_COURSE->value;

                $doctor->update(['status' => $newStatus]);
            }
        }

        $doctors = Doctor::where('status', DoctorStatusEnum::SANCTIONED->value)->get();

        foreach ($doctors as $doctor) {
            $sanction = Sanction::where('doctor_id', $doctor->id)->first();

            Log::info('esta entrando aca');

            if (!isset($sanction->id)) {
                $doctor->update(['status' => DoctorStatusEnum::WITH_INCIDENCE->value]);
            }
        }
    }
}
