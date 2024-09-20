<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\WalletResource;
use App\Repositories\Interfaces\AnnouncementRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $user;
    private $wallet;
    private $announcement;

    public function __construct(WalletRepositoryInterface $wallet, AnnouncementRepositoryInterface $announcement)
    {
        $this->user = new AuthService('user-api');
        $this->wallet = $wallet;
        $this->announcement = $announcement;
    }

    public function index(){
        $user = $this->user->logged_in_user();
        $wallet = $this->wallet->findFirstBy([
            'user_id' => $user->id
        ]);

        $announcements = $this->announcement->index();

        return $this->success_response("Dashboard Details fetched", [
            'account_number' => $user->account_number ?? null,
            'balance' => $wallet ? new WalletResource($wallet) : null,
            'announcements' => AnnouncementResource::collection($announcements)
        ]);
    }
}
