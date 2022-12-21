<?php

namespace App\Http\Controllers;

use App\Models\AgeRange;
use App\Models\Area;
use App\Models\Component;
use App\Models\ComponentUpdate;
use App\Models\Filament;
use App\Models\FilamentUpdate;
use App\Models\Invoice;
use App\Models\LaserUpdate;
use App\Models\MaterialLaser;
use App\Models\MaterialMilling;
use App\Models\MillingUpdate;
use App\Models\Observation;
use App\Models\ReasonVisit;
use App\Models\Report;
use App\Models\Resin;
use App\Models\ResinUpdate;
use App\Models\Software;
use App\Models\Stabilizer;
use App\Models\StabilizerUpdate;
use App\Models\TechExpense;
use App\Models\Thread;
use App\Models\ThreadUpdate;
use App\Models\TypeSex;
use App\Models\Vinyl;
use App\Models\VinylUpdate;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PDF;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Report::with('user')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $report = Report::create([
            'user_id' => $request->user_id,
            'month' => Carbon::parse($request->start_date)->format('M'),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
        ]);

        return response()->json([
            'message' => 'Reporte creado correctamente',
            'success' => true,
            'report' => $report
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        $report->delete();
    }

    public function pdf(Report $report)
    {
        /* ------------------------------- Visits ----------------------------------------------------*/

        $totalVisits = Visit::whereBetween('created_at', [$report->start_date, $report->end_date])->count();
        $totalTime = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->selectRaw('sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->get();

        /* Pivot table */
        $customersTop = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.name, count(*) as total')
            ->groupBy('customer_visit.customer_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $districtsTop = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.district_id, count(*) as total')
            ->groupBy('customers.district_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $districts = Http::get('http://127.0.0.1:8001/api/districts')->collect();

        foreach ($districtsTop as $districtTop) {
            $result = $districts->where('id', $districtTop->district_id)->first();
            $districtTop->district_id = $result['name'];
        }

        /* Multi table */
        $timeDifferentAreas = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->join('areas', 'area_visit.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->groupBy('area_visit.area_id')
            ->orderBy('total', 'desc')
            ->get();

        $areasNotShow = Area::whereNotIn('id', $timeDifferentAreas->pluck('id'))->select('id', 'name')->get();
        $timeDifferentAreas = $timeDifferentAreas->merge($areasNotShow);

        /* Multi table */

        $timeDifferentReasonVisit = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->join('reason_visits', 'visits.reason_visit_id', '=', 'reason_visits.id')
            ->selectRaw('reason_visits.id, reason_visits.name, sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->groupBy('visits.reason_visit_id')
            ->orderBy('total', 'desc')
            ->get();

        $reasonVisitsNotShow = ReasonVisit::whereNotIn('id', $timeDifferentReasonVisit->pluck('id'))->select('id', 'name')->get();
        $timeDifferentReasonVisit = $timeDifferentReasonVisit->merge($reasonVisitsNotShow);

        $ageRangeTotal = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->join('age_ranges', 'customers.age_range_id', '=', 'age_ranges.id')
            ->selectRaw('age_ranges.id, age_ranges.name, count(*) as total')
            ->groupBy('customers.age_range_id')
            ->orderBy('total', 'desc')
            ->get();

        $ageRangesNotShow = AgeRange::whereNotIn('id', $ageRangeTotal->pluck('id'))->select('id', 'name')->get();

        $ageRangeTotal = $ageRangeTotal->merge($ageRangesNotShow);

        $typeSexesTotal = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->join('type_sexes', 'customers.type_sex_id', '=', 'type_sexes.id')
            ->selectRaw('type_sexes.id, type_sexes.name, count(*) as total')
            ->groupBy('customers.type_sex_id')
            ->orderBy('total', 'desc')
            ->get();

        $typeSexesNotShow = TypeSex::whereNotIn('id', $typeSexesTotal->pluck('id'))->select('id', 'name')->get();

        $typeSexesTotal = $typeSexesTotal->merge($typeSexesNotShow);

        $observations = Observation::with('user')->whereBetween('created_at', [$report->start_date, $report->end_date])->get();

        /* ------------------------------- Sales ----------------------------------------------------*/

        /* Informacion basica */

        $totalSalesEvents = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->join('event_invoice', 'invoices.id', '=', 'event_invoice.invoice_id')
            ->count();

        $totalSalesSUM = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->join('sums', 'invoices.id', '=', 'sums.invoice_id')
            ->count();

        $totalSalesSUS = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->join('suss', 'invoices.id', '=', 'suss.invoice_id')
            ->count();

        $totalSalesEmbroidery = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->join('su_embroideries', 'invoices.id', '=', 'su_embroideries.invoice_id')
            ->count();

        $useMachines = $totalSalesSUM + $totalSalesSUS + $totalSalesEmbroidery;

        $totalSalesMaker = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('type_sale', 'M')->count();

        $totalSalesServices = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('type_sale', 'S')->count();

       /* Ingresos y gastos en eventos */

        $eventsExpensesIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->join('event_invoice', 'invoices.id', '=', 'event_invoice.invoice_id')
            ->join('events', 'event_invoice.event_id', '=', 'events.id')
            ->selectRaw('events.name, events.price, events.expenses AS egresos, event_invoice.event_id, sum(events.price) as ingresos')
            ->groupBy('events.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        /* Ingresos en areas */

        $SUMIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->join('sums', 'invoices.id', '=', 'sums.invoice_id')
            ->join('areas', 'sums.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(sums.base_cost) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SUSIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->join('suss', 'invoices.id', '=', 'suss.invoice_id')
            ->join('areas', 'suss.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(suss.base_cost) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SUEIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->join('su_embroideries', 'invoices.id', '=', 'su_embroideries.invoice_id')
            ->join('areas', 'su_embroideries.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(su_embroideries.base_cost) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->merge($SUSIncome);

        $SUMIncome = $SUMIncome->merge($SUEIncome);

        $SUMIncomeNotShow = Area::whereNotIn('id', $SUMIncome->pluck('id'))->select('id', 'name')->get();

        $SUMIncome = $SUMIncome->merge($SUMIncomeNotShow);

        /* Egresos en areas */

        /* Bordadora */

        $threadExpenses = Thread::whereBetween('threads.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(threads.price_purchase) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $threadUpdateExpenses = ThreadUpdate::whereBetween('thread_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(thread_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();


        $stabilizerExpenses = Stabilizer::whereBetween('stabilizers.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(stabilizers.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $stabilizerUpdateExpenses = StabilizerUpdate::whereBetween('stabilizer_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(stabilizer_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($threadExpenses, $stabilizerExpenses, $threadUpdateExpenses, $stabilizerUpdateExpenses) {
            if ($item->id == 8) {
                $item->egresos = $threadExpenses[0]->egresos + $threadUpdateExpenses[0]->egresos + $stabilizerExpenses[0]->egresos + $stabilizerUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* Cortadora de vinilo */

        $vinylExpenses = Vinyl::whereBetween('vinyls.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(vinyls.cost) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();
        
        $vinylUpdateExpenses = VinylUpdate::whereBetween('vinyl_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(vinyl_updates.cost) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($vinylExpenses, $vinylUpdateExpenses) {
            if ($item->id == 4) {
                $item->egresos = $vinylExpenses[0]->egresos + $vinylUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* Electronica */

        $componentsExpenses = Component::whereBetween('components.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(components.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $componentsUpdateExpenses = ComponentUpdate::whereBetween('component_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(component_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($componentsExpenses, $componentsUpdateExpenses) {
            if ($item->id ==1) {
                $item->egresos = $componentsExpenses[0]->egresos + $componentsUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* filaments */

        $filamentsExpenses = Filament::whereBetween('filaments.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(filaments.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $filamentsUpdateExpenses = FilamentUpdate::whereBetween('filament_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(filament_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($filamentsExpenses, $filamentsUpdateExpenses) {
            if ($item->id == 5) {
                $item->egresos = $filamentsExpenses[0]->egresos + $filamentsUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* resins */

        $resinsExpenses = Resin::whereBetween('resins.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(resins.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $resinsUpdateExpenses = ResinUpdate::whereBetween('resin_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(resin_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($resinsExpenses, $resinsUpdateExpenses) {
            if ($item->id == 6) {
                $item->egresos = $resinsExpenses[0]->egresos + $resinsUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* Softwares */

        $softwaresExpenses = Software::whereBetween('softwares.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(softwares.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($softwaresExpenses) {
            if ($item->id == 7) {
                $item->egresos = $softwaresExpenses[0]->egresos;
            }
            return $item;
        });

        /* Materials Laser */

        $materialsLaserExpenses = MaterialLaser::whereBetween('material_lasers.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(material_lasers.cost) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $materialsLaserUpdateExpenses = LaserUpdate::whereBetween('laser_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(laser_updates.cost) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($materialsLaserExpenses, $materialsLaserUpdateExpenses) {
            if ($item->id == 3) {
                $item->egresos = $materialsLaserExpenses[0]->egresos + $materialsLaserUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* Milling */

        $millingExpenses = MaterialMilling::whereBetween('material_millings.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(material_millings.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $millingUpdateExpenses = MillingUpdate::whereBetween('milling_updates.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(milling_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($millingExpenses, $millingUpdateExpenses) {
            if ($item->id == 2) {
                $item->egresos = $millingExpenses[0]->egresos + $millingUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* Gastos tecnicos por area */

        $technicalExpenses = TechExpense::whereBetween('tech_expenses.created_at', [$report->start_date, $report->end_date])
            ->selectRaw('sum(tech_expenses.amount) as egresos, areas.id, areas.name')
            ->join('areas', 'areas.id', '=', 'tech_expenses.area_id')
            ->groupBy('areas.id')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($technicalExpenses) {
            foreach ($technicalExpenses as $technicalExpense) {
                if ($item->id == $technicalExpense->id) {
                    $item->egresos += $technicalExpense->egresos;
                }
            }
            return $item;
        });

        $type = match ($report->type) {
            'i' => function () use ($report, $totalSalesEvents, $useMachines, $SUMIncome, $totalSalesMaker, $totalSalesServices, $eventsExpensesIncome) {
                /* Faltaria calcular totales */
                $pdf = PDF::loadView('inventary', [
                    'report' => $report,
                    'totalSalesEvents' => $totalSalesEvents,
                    'totalSalesSUM' => $useMachines,
                    'SUMIncome' => $SUMIncome,
                    'totalSalesMaker' => $totalSalesMaker,
                    'totalSalesServices' => $totalSalesServices,
                    'eventsExpensesIncome' => $eventsExpensesIncome->sortBy('name')->values()->all(),
                ]);
                return $pdf->stream();
            },
            'v' =>  function () use ($report, $totalVisits, $totalTime, $customersTop, $districtsTop, $timeDifferentAreas, $timeDifferentReasonVisit, $ageRangeTotal, $typeSexesTotal, $observations) {
                $pdf = PDF::loadView('visits', [
                    'report' => $report,
                    'totalVisits' => $totalVisits,
                    'totalTime' => $totalTime[0]->total,
                    'customersTop' => $customersTop,
                    'districtsTop' => $districtsTop,
                    'timeDifferentAreas' => $timeDifferentAreas->sortBy('name')->values()->all(),
                    'timeDifferentReasonVisit' => $timeDifferentReasonVisit->sortBy('name')->values()->all(),
                    'ageRangeTotal' => $ageRangeTotal->sortBy('name')->values()->all(),
                    'typeSexesTotal' => $typeSexesTotal->sortBy('name')->values()->all(),
                    'observations' => $observations,
                ]);
                return $pdf->stream();
            },
            default =>  function () use ($report, $totalVisits, $totalTime, $customersTop, $districtsTop, $timeDifferentAreas, $timeDifferentReasonVisit, $ageRangeTotal, $typeSexesTotal, $observations, $totalSalesEvents, $useMachines, $SUMIncome, $totalSalesMaker, $totalSalesServices, $eventsExpensesIncome) {
                $pdf = PDF::loadView('general', [
                    'report' => $report,
                    'totalVisits' => $totalVisits,
                    'totalTime' => $totalTime[0]->total,
                    'customersTop' => $customersTop,
                    'districtsTop' => $districtsTop,
                    'timeDifferentAreas' => $timeDifferentAreas->sortBy('name')->values()->all(),
                    'timeDifferentReasonVisit' => $timeDifferentReasonVisit->sortBy('name')->values()->all(),
                    'ageRangeTotal' => $ageRangeTotal->sortBy('name')->values()->all(),
                    'typeSexesTotal' => $typeSexesTotal->sortBy('name')->values()->all(),
                    'observations' => $observations,
                    'totalSalesEvents' => $totalSalesEvents,
                    'totalSalesSUM' => $useMachines,
                    'SUMIncome' => $SUMIncome,
                    'totalSalesMaker' => $totalSalesMaker,
                    'totalSalesServices' => $totalSalesServices,
                    'eventsExpensesIncome' => $eventsExpensesIncome->sortBy('name')->values()->all(),
                ]);
                return $pdf->stream();
            }
        };

        return $type();
    }
}
