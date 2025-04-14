<?php

namespace App\Http\Controllers;
use App\Services\PvpService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PvpController extends Controller
{
    private $pvpService;
    public function __construct(PvpService $pvpService)
    {
        $this->pvpService = $pvpService;
    }

    /**
     * Use the PvpService to Get the Battles with waiting status
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPvpBattles(){
        return $this->pvpService->getPvpBattles();

    }

    /**
     * Use the PvpService to Get the play of the host
     * @param $pvpId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHostPlay($pvpId){
        return $this->pvpService->getHostPlay($pvpId);
    }

    /**
     * Use the PvpService to get the history of Pvp Battles
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPvpHistory(){
    return $this->pvpService->getPvpHistory();
    }

    /**
     * Use the PvpService to get the History of the logged In user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyPvpHistory(){
     return $this->pvpService->getMyPvpHistory();
    }

    /**
     * Use the PvpService to assign the play of the hosting user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPlay(Request $request)
    {
        return $this->pvpService->assignPlay($request);
    }

    /**
     * Use the PvpService to let opponent join in the battle
     * @param Request $request
     * @param $pvpId
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinBattle(Request $request, $pvpId)
    {
        return $this->pvpService->joinBattle($request, $pvpId);
    }

    /**
     * Use the PvpService to get the leaderboard order by most wins
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaderboard()
    {
     return $this->pvpService->getLeaderboard();
    }




}
