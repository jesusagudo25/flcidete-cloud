<?php

namespace App\Http\Controllers;

use App\Models\AgeRange;
use App\Models\Area;
use App\Models\Customer;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Invoice;
use App\Models\Report;
use App\Models\TechExpense;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Area::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Area::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        return Area::find($area->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        Area::where('id', $area->id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        //
    }

    public function services()
    {
        $areas = Area::where('active', 1)->get();
        $events = Event::where('active', 1)->get();
        $eventsCategories = EventCategory::where('active', 1)->get();

        return response()->json([
            'a' => $areas,
            'e' => $events,
            'ec' => $eventsCategories
        ]);
    }

    public function graphs()
    {

        //Get first day month
        $firstDayMonth = date('Y-m-01 00:00:00');
        $lastDayMonthCurrent = date('Y-m-t 23:59:59');

        /* Ventas mensuales */

        $invoicesMonth = Invoice::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->where([
            ['status', 'F'],
            ['is_active', 1]
        ])->sum('total');

        /* Nuevos clientes */

        $newCustomers = Customer::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->where('active', 1)->count();

        /* Vistas del mes */

        $visitsMonth = Visit::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->where('active', 1)->count();

        /* Gastos técnicos */

        $expenses = TechExpense::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->where('active', 1)->sum('amount');

        /* Ingresos y gastos por mes*/
        $report = new Report();

        $year = date('Y');

        $firstDaysOfMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            $firstDay = date("Y-m-01", strtotime("$year-$month-01"));
            $firstDaysOfMonth[] = $firstDay;
        }

        $total = [];

        //Recorremos los meses
        foreach ($firstDaysOfMonth as $firstDay) {
            $lastDay = date('Y-m-t', strtotime($firstDay));
            
            $total[] = $report->getIncomeAndExpensesByMonth($firstDay, $lastDay);
        }

        $incomeExpenses = [
            'labels' => $firstDaysOfMonth,
            'data' => $total
        ];

        /* Áreas más visitadas ------------------ */

        $timeDifferentAreas = Visit::whereBetween('visits.created_at', [$firstDayMonth, $lastDayMonthCurrent])
            ->where('visits.active', 1)
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->join('areas', 'area_visit.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->groupBy('area_visit.area_id')
            ->orderBy('total', 'desc')
            ->get()->take(4);

        if ($timeDifferentAreas->sum('total') == 0) {
            $areasPercentage = [
                [
                    'name' => 'No hay datos',
                    'percentage' => 0
                ]
            ];
        } else {
            $areasPercentage = $timeDifferentAreas->map(function ($item, $key) use ($timeDifferentAreas) {
                $item->percentage = round(($item->total * 100) / $timeDifferentAreas->sum('total'), 1);
                return $item;
            });
        }

        /* Distritos frecuentes  --------------  */
        $districtsTop = Visit::whereBetween('visits.created_at', [$firstDayMonth, $lastDayMonthCurrent])
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.district_id, count(*) as total')
            ->groupBy('customers.district_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        if ($districtsTop->count() == 0) {
            $districtsTop = [
                ['district_id' => 'No hay datos', 'total' => 0]
            ];
        } else {
            $districts = Http::get(config('config.geoptyapi') . '/api/districts')->collect();

            foreach ($districtsTop as $districtTop) {
                $result = $districts->where('id', $districtTop->district_id)->first();
                $districtTop->district_id = $result['name'];
            }
        }

        /* Frecuencia de visitantes por género y edad  -------------- */
        $ageRangeByM = Visit::whereBetween('visits.created_at', [$firstDayMonth, $lastDayMonthCurrent])
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->join('age_ranges', 'customers.age_range_id', '=', 'age_ranges.id')
            ->where([
                ['visits.active', 1],
                ['customers.type_sex_id', 1]
            ])
            ->selectRaw('age_ranges.id, age_ranges.name, count(*) as total')
            ->groupBy('customers.age_range_id')
            ->orderBy('age_ranges.id', 'asc')
            ->get();

        $ageRangeByF = Visit::whereBetween('visits.created_at', [$firstDayMonth, $lastDayMonthCurrent])
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->join('age_ranges', 'customers.age_range_id', '=', 'age_ranges.id')
            ->where([
                ['visits.active', 1],
                ['customers.type_sex_id', 2]
            ])
            ->selectRaw('age_ranges.id, age_ranges.name, count(*) as total')
            ->groupBy('customers.age_range_id')
            ->orderBy('age_ranges.id', 'asc')
            ->get();

        $ageRangesNotShowByM = AgeRange::whereNotIn('id', $ageRangeByM->pluck('id'))->selectRaw('id, name, 0 as total')->get();
        $ageRangesNotShowByF = AgeRange::whereNotIn('id', $ageRangeByF->pluck('id'))->selectRaw('id, name, 0 as total')->get();

        $ageRangeByF = $ageRangeByF->merge($ageRangesNotShowByF);
        $ageRangeByM = $ageRangeByM->merge($ageRangesNotShowByM);

        return response()->json([
            'invoicesMonth' => $invoicesMonth,
            'newCustomers' => $newCustomers,
            'visitsMonth' => $visitsMonth,
            'expenses' => $expenses,
            'incomeExpenses' => $incomeExpenses,
            'areasPercentage' => $areasPercentage,
            'districtsTop' => $districtsTop,
            'ageRangeByM' => $ageRangeByM,
            'ageRangeByF' => $ageRangeByF,
        ]);
    }
}
