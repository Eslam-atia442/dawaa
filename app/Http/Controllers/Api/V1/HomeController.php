<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EgratesApiService;
use App\Traits\BaseApiResponseTrait;
use App\Enums\FundCategoryTypeEnum;
use App\Models\GoldFund;
use App\Models\Certificate;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\GoldFundResource;
use Illuminate\Support\Facades\Cache;

/**
 * @group Api
 * @subgroup Home
 */
class HomeController extends Controller
{
    use BaseApiResponseTrait;

    public array $relations;


    /**
     * Home items.
     * param Keyword for search.
     *
     */
    public function index(): mixed{

        request()->merge(['scope' => 'full']);

        $latestUpdatedGoldFund = GoldFund::with('latestPriceLog', 'assetManagementCompany', 'goldFundType', 'riskLevel', 'currency')->where('fund_category_type', FundCategoryTypeEnum::GOLD->value)
            ->where('is_active', 1)->latest('updated_at')->take(1)->first();

        $latestUpdatedStocksFund = GoldFund::with('latestPriceLog', 'assetManagementCompany', 'goldFundType', 'riskLevel', 'currency')->where('fund_category_type', FundCategoryTypeEnum::STOCKS->value)
            ->where('is_active', 1)->latest('updated_at')->take(1)->first();

        $latestUpdatedRealEstateFund = GoldFund::with('latestPriceLog', 'assetManagementCompany', 'goldFundType', 'riskLevel', 'currency')->where('fund_category_type', FundCategoryTypeEnum::REAL_ESTATE->value)
            ->where('is_active', 1)->latest('updated_at')->take(1)->first();

        $latestUpdatedCashFund = GoldFund::with('latestPriceLog', 'assetManagementCompany', 'goldFundType', 'riskLevel', 'currency')->where('fund_category_type', FundCategoryTypeEnum::CASH->value)
            ->where('is_active', 1)
            ->latest('updated_at')->take(1)->first();

        $highestCertificate = Certificate::with('certificationType', 'bank', 'purchaseMethods', 'riskLevel')->where('is_active', 1)
            ->orderBy('interest_rate', 'desc')
            ->latest('updated_at')->take(1)->first();

        $gold_prices     = Cache::get('egrates_gold_prices');
        $currency_prices = app(EgratesApiService::class)->getAllCachedCurrencyPrices();


        return $this->respondWithArray([
            'certificate'      => $highestCertificate ? new CertificateResource($highestCertificate) : null,
            'gold_fund'        => $latestUpdatedGoldFund ? new GoldFundResource($latestUpdatedGoldFund) : null,
            'stocks_fund'      => $latestUpdatedStocksFund ? new GoldFundResource($latestUpdatedStocksFund) : null,
            'real_estate_fund' => $latestUpdatedRealEstateFund ? new GoldFundResource($latestUpdatedRealEstateFund) : null,
            'cash_fund'        => $latestUpdatedCashFund ? new GoldFundResource($latestUpdatedCashFund) : null,
            'gold_prices'      => $gold_prices ? $gold_prices : null,
            'currency_prices'  => $currency_prices ? $currency_prices : null,
        ]);

    }

}
