<?php

namespace App\Http\Controllers;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\alert;
use function Laravel\Prompts\error;

class MarketController extends Controller
{
    private MarketService $marketService;
    public function __construct(MarketService $marketService){
        $this->marketService = $marketService;
    }

    /**
     * Use MarketService to get the Active Listing in the market
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActive()
    {
        return $this->marketService->getActive();
    }

    /**
     * Use MarketService to Sell Item in the market
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function sellItem(Request $request)
    {
        return $this->marketService->sellItem($request);
    }

    /**
     * Use MarketService to get the user's item for sale
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserItemForSale(Request $request)
    {
      return $this->marketService->getUserItemForSale($request);
    }

    /**
     * Use MarketService to cancel listing in market
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelListing(Request $request)
    {
       return $this->marketService->cancelListing($request);
    }

    /**
     * Use MarketService to buy item
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyItem(Request $request)
    {
       return $this->marketService->buyItem($request);
    }

    /**
     * Use MarketService to get the Selected Item History
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItemHistory(Request $request)
    {
       return $this->marketService->getItemHistory($request);
    }
}


