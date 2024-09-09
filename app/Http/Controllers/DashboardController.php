<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(public readonly DashboardRepository $dashboardRepository)
    {
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return view('dashboard', $this->dashboardRepository->getDashboardData());
    }
}
