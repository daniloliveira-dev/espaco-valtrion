<?php

namespace App\Http\Controllers;

use App\DTOs\Patients\PatientSearchData;
use App\Http\Requests\Patients\IndexPatientsRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\UseCases\Patients\GetPatientDetailsUseCase;
use App\UseCases\Patients\SearchPatientsUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PatientController extends Controller
{
    public function index(
        IndexPatientsRequest $request,
        SearchPatientsUseCase $searchPatients,
    ): AnonymousResourceCollection {
        return PatientResource::collection(
            $searchPatients->execute(PatientSearchData::fromArray($request->validated())),
        );
    }

    public function show(Patient $patient, GetPatientDetailsUseCase $getPatientDetails): PatientResource
    {
        return new PatientResource($getPatientDetails->execute($patient->id));
    }
}
