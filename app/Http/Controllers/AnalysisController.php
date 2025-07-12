<?php

namespace App\Http\Controllers;

use App\Models\FilteredActivity;
use App\Models\Setting;
use App\Traits\Utilities;

class AnalysisController extends Controller
{
    use Utilities;

    private $analyses = [
        ['id' => 0, 'name' => 'Average Heartrate', 'active' => 0],
        ['id' => 1, 'name' => 'Average Watts', 'active' => 0],
        ['id' => 2, 'name' => 'Average Climbing', 'active' => 0],
        ['id' => 3, 'name' => 'Average Distance', 'active' => 0],
        ['id' => 4, 'name' => 'Average Speed', 'active' => 0],
        ['id' => 5, 'name' => 'Total Climbing', 'active' => 0],
        ['id' => 6, 'name' => 'Total Distance', 'active' => 0],
        ['id' => 7, 'name' => 'Average Heartrate compared to Climbing', 'active' => 0],
        ['id' => 8, 'name' => 'Average Heartrate compared to Distance', 'active' => 0],
        ['id' => 9, 'name' => 'Average Heartrate compared to Speed', 'active' => 0],
        ['id' => 10, 'name' => 'Average Heartrate compared to Watts', 'active' => 0],
        ['id' => 11, 'name' => 'Ratio of climbing to distance', 'active' => 0],
    ];

    private $analysis_ids = [];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    private function getAnalyses()
    {
        return $this->analysisIdSetbyUser() ?: $this->analysisIdSetInDB();
    }

    private function analysisIdSetbyUser()
    {
        $analysis_id = request()->input('analysis_selected');
        if (is_null($analysis_id)) {
            return false;
        }

        $analysis_ids = [request()->input('second_analysis') == 'true' ? true : false, (int) $analysis_id, (int) request()->input('analysis2_selected')];
        $this->saveAnalysisIdToDB($analysis_ids);

        return $analysis_ids;

    }

    private function analysisIdSetInDB()
    {
        return Setting::current_analysis_id();
    }

    private function saveAnalysisIdToDB($analysis_ids)
    {
        Setting::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'analysis_id' => $analysis_ids,
            ]
        );
    }

    private function getTimeperiod()
    {
        $timeperiod = $this->timeperiodSetbyUser();
        if ($timeperiod === false) {
            $timeperiod = $this->timeperiodSetInDB();
        }

        return $timeperiod;
    }

    private function timeperiodSetbyUser()
    {
        $timeperiod = request()->input('time_period');
        if (is_null($timeperiod)) {
            return false;
        }

        $this->saveTimeperiodToDB($timeperiod);

        return (int) $timeperiod;

    }

    private function timeperiodSetInDB()
    {
        return Setting::current_timeperiod();
    }

    private function saveTimeperiodToDB($timeperiod)
    {
        Setting::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'time_period' => $timeperiod,
            ]
        );
    }

    private function getDataset($timeperiod)
    {
        $selectStatement =
                'round(AVG(average_heartrate), 0) as `AverageHR`,
                round(avg(total_elevation_gain),0) as `Climbing`,
                round(sum(total_elevation_gain),0) as `TotalClimbing`,
                round(AVG(average_heartrate)/avg(total_elevation_gain),2) as `HRtoClimbing`,
                round(avg(distance),0) as `Distance`,
                round(sum(distance),0) as `TotalDistance`,
                round(AVG(average_heartrate)/avg(distance),1) as `HRtoDistance`,
                round(avg(average_speed),0) as `Speed`,
                round(AVG(average_heartrate)/avg(average_speed),1) as `HRtoSpeed`,
                round(avg(average_watts),0) as `Watts`,
                round(AVG(average_heartrate)/avg(total_elevation_gain),2) as `HRtoWatts`,
                round(sum(total_elevation_gain)/sum(distance)/10,2) as `ClimbingToDistance`,';

        $selectStatement .= $this->addGroupByColumn($timeperiod);

        $dataset = FilteredActivity::groupBy('groupby')
            ->selectRaw($selectStatement)
            ->orderBy('groupby')
            ->get();

        return $dataset;
    }

    // timePeriods = [
    //     {id: 0, name: 'Year'},
    //     {id: 1, name: 'Half Year'},
    //     {id: 2, name: 'Every 3 months'},
    //     {id: 3, name: 'Month'},
    // ]
    private function addGroupByColumn($timeperiod)
    {
        switch ($timeperiod) {
            case 1: $groupByString = "concat(strftime('%Y', start_date_local ), '_',floor((strftime('%m', start_date_local)-1)/6)+1)";
                break;
            case 2: $groupByString = "concat(strftime('%Y', start_date_local ), '_',floor((strftime('%m', start_date_local)-1)/3)+1)";
                break;
            case 3: $groupByString = "concat(strftime('%Y', start_date_local ), '_',strftime('%m', start_date_local))";
                break;
            default: $groupByString = "strftime('%Y', start_date_local )";

        }
        $groupByString .= " as 'groupby'";

        return $groupByString;
    }

    private function getChartOptions($analysisIds)
    {

        $scales = ['A' => ['type' => 'linear', 'position' => 'left']];
        if ($analysisIds[0]) {
            $scales = array_merge($scales, ['B' => ['type' => 'linear', 'position' => 'right']]);
        }

        $chartOptions = [
            'responsive' => true,
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'stacked' => false,
            'plugins' => [
                'tooltip' => ['position' => 'nearest'],
                'legend' => [
                    'position' => 'bottom',
                    'labels' => ['font' => ['size' => 14],
                    ],
                ],
                'title' => ['display' => true, 'text' => 'My Exercises Compared Over Time', 'font' => ['size' => 18]],
            ],
            'scales' => $scales,
        ];

        return $chartOptions;
    }

    private function getChartData($dataset, $analysisIds)
    {
        $labels = [];
        $data = [];
        $data2 = [];
        foreach ($dataset as $row) {
            $labels[] = $row->groupby;
            $data[] = $this->getRowData($row, $analysisIds[1]);
            $data2[] = $this->getRowData($row, $analysisIds[2]);
        }
        $basic_dataset = ['pointStyle' => 'rect', 'pointRadius' => 7, 'pointHoverRadius' => 15, 'fill' => false, 'tension' => 0.1];
        $dataset1 = array_merge($basic_dataset, ['label' => $this->getLabel($analysisIds[1]), 'data' => $data, 'yAxisID' => 'A', 'borderColor' => 'rgb(75, 192, 192)']);
        $dataset2 = array_merge($basic_dataset, ['label' => $this->getLabel($analysisIds[2]), 'data' => $data2, 'yAxisID' => 'B', 'borderColor' => 'rgb(255, 0, 127)']);

        $second_analysis = $analysisIds[0];

        return $second_analysis ? ['labels' => $labels, 'datasets' => [$dataset1, $dataset2]] : ['labels' => $labels, 'datasets' => [$dataset1]];
    }

    private function getRowData($row, $analysisId)
    {
        switch ($analysisId) {
            case 1: return $row->Watts;
            case 2: return $row->Climbing;
            case 3: return $row->Distance;
            case 4: return $row->Speed;
            case 5: return $row->TotalClimbing;
            case 6: return $row->TotalDistance;
            case 7: return $row->HRtoClimbing;
            case 8: return $row->HRtoDistance;
            case 9: return $row->HRtoSpeed;
            case 10: return $row->HRtoWatts;
            case 11: return $row->ClimbingToDistance;
            default: return $row->AverageHR;
        }
    }

    private function getLabel($analysisId)
    {
        switch ($analysisId) {
            case 1: return 'Average Watts';
            case 2: return 'Average Climbing';
            case 3: return 'Average Distance';
            case 4: return 'Average Speed';
            case 5: return 'Total Climbing';
            case 6: return 'Total Distance';
            case 7: return 'Ratio of HR to Climbing';
            case 8: return 'Ratio of HR to Distance';
            case 9: return 'Ratio of HR to Speed';
            case 10: return 'Ratio of HR to Watts';
            case 11: return 'Ratio of Climbing to Distance';
            default: return 'Average Heartrate';
        }
    }

    public function api_get()
    {
        $timeperiod = $this->getTimePeriod();
        $dataset = $this->getDataset($timeperiod);
        $this->analysis_ids = $this->getAnalyses();
        $chartData = $this->getChartData($dataset, $this->analysis_ids, $timeperiod);

        return response()->json([
            'dataset' => $dataset,
            'analyses' => $this->analyses,
            'chartData' => $chartData,
            'timeperiod' => $timeperiod,
            'activeanalysisids' => $this->analysis_ids,
            'chartOptions' => $this->getChartOptions($this->analysis_ids),
        ],
            201
        );
    }

    public function api_post()
    {
        $timeperiod = request()->input('time_period');
        $analysis_id = request()->input('analysis_selected');
        $analysis_ids = [request()->input('second_analysis') == 'true' ? true : false, (int) $analysis_id, (int) request()->input('analysis2_selected')];
        Setting::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'time_period' => $timeperiod,
                'analysis_id' => $analysis_ids,
            ]
        );

    }
}
