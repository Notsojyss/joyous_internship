<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PvpController extends Controller
{
    public function getPvpBattles(){
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $pvpbattles = DB::table('pvp')
            ->join('users', 'pvp.host_id', '=', 'users.id')
            ->where('pvp.status', 'waiting')
            ->select(
                'pvp.host_id',
                'pvp.id',
                'pvp.money_betted',
//                'pvp.host_play as hostplay',
                'users.id as users_id',
                'users.username as username'
            )
            ->get();

        return response()->json($pvpbattles);

    }
    public function getHostPlay($pvpId){
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $pvp = DB::table('pvp')
            ->where('id', $pvpId)
            ->select('host_play')
            ->first();

        return response()->json($pvp);
    }
    public function getPvpHistory(){
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $pvphistory = DB::table('pvp')
            ->join('users as host', 'pvp.host_id', '=', 'host.id')
            ->join('users as opponent', 'pvp.opponent_id', '=', 'opponent.id')
            ->join('users as winner', 'pvp.winner_id', '=', 'winner.id')
            ->where('pvp.status', 'finished')
            ->select(
                'host.username as hostname',
                'opponent.username as opponentname',
                'pvp.money_betted',
                'winner.username as winnername',
                'pvp.updated_at as battletime'
            )
            ->orderByDesc('pvp.updated_at')
            ->get();

        return response()->json($pvphistory);
    }

    public function assignPlay(Request $request)
    {
        try {
            $request->validate([
                'play' => 'required|in:Rock,Paper,Scissor',
                'money_betted' => 'required|numeric|min:100',
            ]);

            // Get authenticated user
            $user = auth()->user();
            $money_betted = $request->money_betted;

            // Check if user has enough money
            if ($user->money < $money_betted) {
                return response()->json(['error' => 'Not enough money'], 400);
            }

            // Use a transaction to prevent partial updates
            DB::transaction(function () use ($user, $money_betted, $request) {
                // Deduct money from user
                DB::table('users')->where('id', $user->id)->decrement('money', $money_betted);

                // Insert PVP play record
                DB::table('pvp')->insert([
                    'host_id' => $user->id,
                    'host_play' => $request->play,
                    'money_betted' => $money_betted,
                    'status' => 'waiting', // Default status
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return response()->json([
                'message' => 'Play assigned successfully!',
                'play' => $request->play,
                'money_betted' => $money_betted,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function joinBattle(Request $request, $pvpId)
    {
        try {
            $request->validate([
                'play' => 'required|in:Rock,Paper,Scissor',
            ]);

            $user = auth()->user();
            $pvp = DB::table('pvp')->where('id', $pvpId)->where('status', 'waiting')->first();

            if (!$pvp) {
                return response()->json(['error' => 'PVP battle not found or already finished'], 404);
            }

            if ($pvp->host_id === $user->id) {
                return response()->json(['error' => 'You cannot join your own battle'], 400);
            }

            if (!is_null($pvp->opponent_id)) {
                return response()->json(['error' => 'This battle already has an opponent'], 400);
            }

            if ($user->money < $pvp->money_betted) {
                return response()->json(['error' => 'Not enough money to join'], 400);
            }

            // Deduct money from the opponent
            DB::transaction(function () use ($user, $pvp, $request) {
                DB::table('users')->where('id', $user->id)->decrement('money', $pvp->money_betted);

                // Update PVP battle
                DB::table('pvp')->where('id', $pvp->id)->update([
                    'opponent_id' => $user->id,
                    'opponent_play' => $request->play,
                    'status' => 'finished', // Battle is now complete
                    'updated_at' => now(),
                ]);
            });

            // Determine winner
            return $this->determineWinner($pvpId);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    private function determineWinner($pvpId)
    {
        $pvp = DB::table('pvp')->where('id', $pvpId)->first();

        if (!$pvp || !$pvp->opponent_id || !$pvp->opponent_play) {
            return response()->json(['error' => 'Invalid PVP match'], 400);
        }

        $rules = [
            'Rock' => 'Scissor',
            'Paper' => 'Rock',
            'Scissor' => 'Paper'
        ];

        $winnerId = null;
        $winnerMessage = "It's a draw! No one wins.";
        $prizeMoney = $pvp->money_betted * 2;

        if ($rules[$pvp->host_play] === $pvp->opponent_play) {
            $winnerId = $pvp->host_id;
            $winnerMessage = "You Lose !";
        } elseif ($rules[$pvp->opponent_play] === $pvp->host_play) {
            $winnerId = $pvp->opponent_id;
            $winnerMessage = "You win! You gained! " . $prizeMoney;
        }

        if ($winnerId) {
            DB::transaction(function () use ($pvpId, $winnerId, $prizeMoney) {
                DB::table('pvp')->where('id', $pvpId)->update([
                    'winner_id' => $winnerId,
                    'status' => 'finished',
                    'updated_at' => now(),
                ]);

                DB::table('users')->where('id', $winnerId)->increment('money', $prizeMoney);
            });
        } else { // It's a draw, refund both players
            DB::transaction(function () use ($pvp) {
                DB::table('users')->where('id', $pvp->host_id)->increment('money', $pvp->money_betted);
                DB::table('users')->where('id', $pvp->opponent_id)->increment('money', $pvp->money_betted);
            });
        }

        return response()->json([
            'message' => $winnerMessage,
            'winner_id' => $winnerId,


        ]);

    }
    public function getLeaderboard()
    {
        $leaderboard = DB::table('pvp')
            ->join('users', 'pvp.winner_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.username',
                DB::raw('COUNT(pvp.winner_id) as wins'),
                DB::raw('SUM(pvp.money_betted) as total_money_won') // Sum only for winners
            )
            ->whereNotNull('pvp.winner_id') // Exclude unfinished or drawn matches
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('wins')
            ->orderByDesc('total_money_won')
            ->orderBy('users.id')
            ->limit(10) // Adjust limit as needed
            ->get();

        return response()->json($leaderboard);
    }




}
