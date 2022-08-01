<?php
namespace app\commands;

use app\models\Region;
use yii\base\InvalidParamException;
use yii\console\Controller;


class ReportcacheController extends Controller
{
    public function actionIndex() {
        $model = new Region();

        echo "cron service runnning...";

        $regions = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];

        // Updating National Team data
        $responseData = [
            'mdSignupTrendDaily' => $model->mdSignupTrendDaily('', '', ''),
            'mdSignupTrendWeekly' => $model->mdSignupTrendWeekly('', '', ''),
            'mdSignupTrendMonthly' => $model->mdSignupTrendMonthly(''),
            'retSignupTrendDaily' => $model->retSignupTrendDaily('', '', ''),
            'retSignupTrendWeekly' => $model->retSignupTrendWeekly('', '', ''),
            'retSignupTrendMonthly' => $model->retSignupTrendMonthly(''),
            'mdLoginLogoutsDaily' => $model->mdLoginLogoutsDaily('', '', ''),
            'mdLoginLogoutWeekly' => $model->mdLoginLogoutWeekly(''),
            'mdLoginLogoutMonthly' => $model->mdLoginLogoutMonthly(''),
            'retLoginLogoutsDaily' => $model->retLoginLogoutsDaily('', '', ''),
            'retLoginLogoutWeekly' => $model->retLoginLogoutWeekly(''),
            'retLoginLogoutMonthly' => $model->retLoginLogoutMonthly(''),
            'mdLangSelection' => $model->mdLangSelection(''),
            'retLangSelection' => $model->retLangSelection(''),
            'mdSignupsAcrossUnit' => $model->mdSignupsAcrossUnit('', '', ''),
            'retSignupsAcrossUnit' => $model->retSignupsAcrossUnit('', '', ''),
            'activeDormantMD' => $model->activeDormantMD('', '', ''),
            'activeDormantRetailers' => $model->activeDormantRetailers('', '', ''),
            'mdActivations' => $model->mdActivations('', '', ''),
            'retActivitiesCompletion' => $model->retActivitiesCompletion('', '', ''),
            'wowEngagedMD' => $model->wowEngagedMD(),
            'wowEngagedRetailer' => $model->wowEngagedRetailer(),
            'mdActivationTrendDaily' => $model->mdActivationTrendDaily(''),
            'mdActivationTrendWeekly' => $model->mdActivationTrendWeekly(''),
            'mdActivationTrendMonthly' => $model->mdActivationTrendMonthly(''),
            'retActivitiesCompletionTrendDaily' => $model->retActivitiesCompletionTrendDaily(''),
            'retActivitiesCompletionTrendWeekly' => $model->retActivitiesCompletionTrendWeekly(''),
            'retActivitiesCompletionTrendMonthly' => $model->retActivitiesCompletionTrendMonthly(''),
            'retActivityTypeEngagement' => $model->retActivityTypeEngagement(''),
            'dailyWofEngagement' => $model->dailyWofEngagement(''),
            'weeklyWofEngagement' => $model->weeklyWofEngagement(''),
            'mdWinnings' => $model->mdWinnings('', '', ''),
            'retWinnings' => $model->retWinnings('', '', ''),
            'top10Retailers' => $model->top10Retailers('', '', ''),
            'top10MDs' => $model->top10MDs('', '', ''),
            'top10SEs' => $model->top10SEs('', '', ''),
            'top10ASMs' => $model->top10ASMs(),
            'top10SM' => $model->top10SM(),
            'top10SH' => $model->top10SH(),
            'top10Units' => $model->top10Units(),
        ];

        $data = \GuzzleHttp\json_encode($responseData);
        $model->saveCachedData(500, $data, 1);

        // Updating ASM data
        foreach ($regions as $region) {
            $responseDataASM = [
                'mdActivationTrendDaily' => $model->mdActivationTrendDaily($region),
                'mdActivationTrendWeekly' => $model->mdActivationTrendWeekly($region),
                'mdActivationTrendMonthly' => $model->mdActivationTrendMonthly($region),
                'retActivitiesCompletionTrendDaily' => $model->retActivitiesCompletionTrendDaily($region),
                'retActivitiesCompletionTrendWeekly' => $model->retActivitiesCompletionTrendWeekly($region),
                'retActivitiesCompletionTrendMonthly' => $model->retActivitiesCompletionTrendMonthly($region),
                'dailyWofEngagement' => $model->dailyWofEngagement($region),
                'weeklyWofEngagement' => $model->weeklyWofEngagement($region),
                'top10Retailers' => $model->top10Retailers('', '', $region),
                'top10MDs' => $model->top10MDs('', '', $region),
                'top10SEs' => $model->top10SEs('', '', $region),
                'top10ASMs' => $model->top10ASMs(),
            ];

            $dataASM = \GuzzleHttp\json_encode($responseDataASM);
            $model->saveCachedData(70, $dataASM, $region);
        }

        // Updating SH data
        foreach ($regions as $region) {
            $responseDataSH = [
                'mdSignupsAcrossUnitSH' => $model->mdSignupsAcrossUnitSH('', '', $region),
                'retSignupsAcrossUnitSH' => $model->retSignupsAcrossUnitSH('', '', $region),
                'activeDormantMDSH' => $model->activeDormantMDSH('', '', $region),
                'activeDormantRetailersSH' => $model->activeDormantRetailersSH('', '', $region),
                'mdActivationsSH' => $model->mdActivationsSH('', '', $region),
                'retActivitiesCompletionSH' => $model->retActivitiesCompletionSH('', '', $region),
                'mdActivationTrendDaily' => $model->mdActivationTrendDaily($region),
                'mdActivationTrendWeekly' => $model->mdActivationTrendWeekly($region),
                'mdActivationTrendMonthly' => $model->mdActivationTrendMonthly($region),
                'retActivitiesCompletionTrendDaily' => $model->retActivitiesCompletionTrendDaily($region),
                'retActivitiesCompletionTrendWeekly' => $model->retActivitiesCompletionTrendWeekly($region),
                'retActivitiesCompletionTrendMonthly' => $model->retActivitiesCompletionTrendMonthly($region),
                'dailyWofEngagement' => $model->dailyWofEngagement($region),
                'weeklyWofEngagement' => $model->weeklyWofEngagement($region),
                'top10Retailers' => $model->top10Retailers('', '', $region),
                'top10MDs' => $model->top10MDs('', '', $region),
                'top10SEs' => $model->top10SEs('', '', $region),
                'top10SH' => $model->top10SH(),
                'mdWinningsSH' => $model->mdWinningsSH('', '', $region),
                'retWinningsSH' => $model->retWinningsSH('', '', $region),
            ];

            $dataSH = \GuzzleHttp\json_encode($responseDataSH);
            $model->saveCachedData(50, $dataSH, $region);
        }

        echo "cron process completed";
    }
}