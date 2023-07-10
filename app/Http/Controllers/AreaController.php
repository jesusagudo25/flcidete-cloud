<?php

namespace App\Http\Controllers;

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
        $firstDayMonth = date('Y-m-01');
        $lastDayMonthCurrent = date('Y-m-t 23:59:59');

        /* Ventas mensuales */

        $invoicesMonth = Invoice::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->where('status', 'F')->sum('total');

        /* Nuevos clientes */

        $newCustomers = Customer::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->count();

        /* Pagos en curso */

        $payments = Invoice::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->where([
            ['type_invoice', '=', 'A'],
            ['status', '=', 'A'],
        ])->count();

        /* Gastos técnicos */

        $expenses = TechExpense::whereBetween('created_at', [$firstDayMonth, $lastDayMonthCurrent])->sum('amount');


        /* Ingresos y gastos por mes*/
        $previousMonths = [];
        $previousMonthLabels = [];
        $nextMonths = [];

        if (date('m') == 1) {
            /* next */
            foreach (range(2, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 2) {
            /* Previous */
            foreach (range(1, 1) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(3, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 3) {
            /* Previous */
            foreach (range(1, 2) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(4, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 4) {
            /* Previous */
            foreach (range(1, 3) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(5, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 5) {
            /* Previous */
            foreach (range(1, 4) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(6, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 6) {
            /* Previous */
            foreach (range(1, 5) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(7, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 7) {
            /* Previous */
            foreach (range(1, 6) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(8, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 8) {
            /* Previous */
            foreach (range(1, 7) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(9, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 9) {
            /* Previous */
            foreach (range(1, 8) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(10, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 10) {
            /* Previous */
            foreach (range(1, 9) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(11, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 11) {
            /* Previous */
            foreach (range(1, 10) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " -$month months"));
            }
            /* next */
            foreach (range(12, 12) as $month) {
                $nextMonths[] = date('Y-m-d', strtotime(date('Y-m-01') . " +$month months"));
            }
        } else if (date('m') == 12) {
            /* Previous */
            foreach (range(0, 11) as $month) {
                $previousMonths[] = date('Y-m-d', strtotime(date('Y-01-01') . " +$month months"));
                //mm/dd/yyyy
                $previousMonthLabels[] = date('m/d/Y', strtotime(date('Y-01-15') . " +$month months"));
            }
        }

        $total = [];
        $report = new Report();

        foreach ($previousMonths as $previousMonth) {
            /* last day date */
            $lastDayMonth = date('Y-m-t', strtotime($previousMonth));
            $total[] = $report->getIncomeAndExpensesByMonth($previousMonth, $lastDayMonth);
        }

        $incomeExpenses = [
            'labels' => $previousMonthLabels,
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
        
        
    
        if($timeDifferentAreas->sum('total') == 0){
            $areasPercentage = [];
        }
        else{
            $areasPercentage = $timeDifferentAreas->map(function ($item, $key) use ($timeDifferentAreas) {
                $item->percentage = round(($item->total * 100) / $timeDifferentAreas->sum('total'), 1);
                return $item;
            });
        }

        /* Distritos frecuentes  --------------  */
        /* Por reparar: Hubo cambios en la tabla customers y subsidiaries */
        $districtsTop = Visit::whereBetween('visits.created_at', [$firstDayMonth, $lastDayMonthCurrent])
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.district_id, count(*) as total')
            ->groupBy('customers.district_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

/*         $districts = Http::get(config('config.geoptyapi').'/api/districts')->collect();

        foreach ($districtsTop as $districtTop) {
            $result = $districts->where('id', $districtTop->district_id)->first();
            $districtTop->district_id = $result['name'];
        } */

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
        
     return response()->json([
            'invoicesMonth' => $invoicesMonth,
            'newCustomers' => $newCustomers,
            'payments' => $payments,
            'expenses' => $expenses,
            'incomeExpenses' => $incomeExpenses,
            'areasPercentage' => $areasPercentage,
            'districtsTop' => $districtsTop,
            'ageRangeByM' => $ageRangeByM,
            'ageRangeByF' => $ageRangeByF,
        ]);      
    }
}
