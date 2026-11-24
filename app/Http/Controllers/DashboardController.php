<?php

namespace App\Http\Controllers;

use App\UseCases\Dashboard\GetDashboardUseCase;
use Illuminate\Http\JsonResponse;

final class DashboardController extends Controller
{
    public function __invoke(GetDashboardUseCase $dashboard): JsonResponse
    {
        return response()->json($dashboard->execute());
    }
}
