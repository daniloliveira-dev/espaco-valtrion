<?php

namespace App\UseCases\Dashboard;

use App\Interfaces\DashboardRepositoryInterface;

final class GetDashboardUseCase
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboardRepository,
    ) {}

    /** @return array<string, mixed> */
    public function execute(): array
    {
        return $this->dashboardRepository->getOverview();
    }
}
