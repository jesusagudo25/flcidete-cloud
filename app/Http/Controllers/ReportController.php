<?php

namespace App\Http\Controllers;

use App\Models\AgeRange;
use App\Models\Area;
use App\Models\Booking;
use App\Models\Component;
use App\Models\ComponentUpdate;
use App\Models\Event;
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
use App\Models\SoftwareUpdate;
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
use Illuminate\Support\Facades\DB;
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
        $months = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];

        $report = Report::create([
            'user_id' => $request->user_id,
            'month' => $months[Carbon::parse($request->start_date)->format('m')],
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
        /* Add 23:60 hours to end_date */

        $report->end_date = Carbon::parse($report->end_date)->addHours(23)->addMinutes(59)->toDateTimeString();

        $totalVisits = Visit::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('active', 1)
            ->count(); 

        $totalTime = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->selectRaw('sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->get();

        /* Pivot table */
        $customersTop = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.name, count(*) as total')
            ->groupBy('customer_visit.customer_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

        $districtsTop = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.district_id, count(*) as total')
            ->groupBy('customers.district_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

        $districts = Http::get('http://127.0.0.1:8001/api/districts')->collect();

        foreach ($districtsTop as $districtTop) {
            $result = $districts->where('id', $districtTop->district_id)->first();
            $districtTop->district_id = $result['name'];
        }

        /* Multi table */
        $timeDifferentAreas = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
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
            ->where('visits.active', 1)
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->join('reason_visits', 'visits.reason_visit_id', '=', 'reason_visits.id')
            ->selectRaw('reason_visits.id, reason_visits.name, sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->groupBy('visits.reason_visit_id')
            ->orderBy('total', 'desc')
            ->get();

        $reasonVisitsNotShow = ReasonVisit::whereNotIn('id', $timeDifferentReasonVisit->pluck('id'))->select('id', 'name')->get();
        $timeDifferentReasonVisit = $timeDifferentReasonVisit->merge($reasonVisitsNotShow);

        $ageRangeTotal = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
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
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->join('type_sexes', 'customers.type_sex_id', '=', 'type_sexes.id')
            ->selectRaw('type_sexes.id, type_sexes.name, count(*) as total')
            ->groupBy('customers.type_sex_id')
            ->orderBy('total', 'desc')
            ->get();

        $typeSexesNotShow = TypeSex::whereNotIn('id', $typeSexesTotal->pluck('id'))->select('id', 'name')->get();

        $typeSexesTotal = $typeSexesTotal->merge($typeSexesNotShow);

        $observations = Observation::with('user')->whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('active', 1)
            ->get();

        /* Bookings */

        $totalBookings = Booking::whereBetween('created_at', [$report->start_date, $report->end_date])->count();

        /* Booking status D */

        $totalBookingsD = Booking::whereBetween('date', [$report->start_date, $report->end_date])
            ->where('status', 'D')
            ->count();

        /* Booking status C */

        $totalBookingsC = Booking::whereBetween('date', [$report->start_date, $report->end_date])
            ->where('status', 'C')
            ->count();

        /* ------------------------------- Sales ----------------------------------------------------*/

        /* Informacion basica */

        $totalSalesEvents = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('status','F')
            ->join('event_invoice', 'invoices.id', '=', 'event_invoice.invoice_id')
            ->count();

        $totalSalesSUM = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('status','F')
            ->join('sums', 'invoices.id', '=', 'sums.invoice_id')
            ->count();

        $totalSalesSUS = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('status','F')
            ->join('suss', 'invoices.id', '=', 'suss.invoice_id')
            ->count();

        $totalSalesEmbroidery = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('status','F')
            ->join('su_embroideries', 'invoices.id', '=', 'su_embroideries.invoice_id')
            ->count();

        $useMachines = $totalSalesSUM + $totalSalesSUS + $totalSalesEmbroidery;

        $totalSalesMaker = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where([
                ['status', 'F'],
                ['type_sale', 'M']
            ])->count();

        $totalSalesServices = Invoice::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where([
                ['status', 'F'],
                ['type_sale', 'S']
            ])->count();

        /* Ingresos y gastos en eventos */

        $eventsExpensesIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where('invoices.status', 'F')
            ->join('event_invoice', 'invoices.id', '=', 'event_invoice.invoice_id')
            ->join('events', 'event_invoice.event_id', '=', 'events.id')
            ->selectRaw('events.name, events.price, events.expenses AS egresos, event_invoice.event_id, sum(events.price) as ingresos')
            ->groupBy('events.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        /* Ingresos en areas */

        $SUMIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where('invoices.status', 'F')
            ->join('sums', 'invoices.id', '=', 'sums.invoice_id')
            ->join('areas', 'sums.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(sums.base_cost) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SUSIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where('invoices.status', 'F')
            ->join('suss', 'invoices.id', '=', 'suss.invoice_id')
            ->join('areas', 'suss.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(suss.base_cost) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SUEIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where('invoices.status', 'F')
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


        /* Egresos en areas ----------------------- Donaciones*/

        /* Tech expenses - name and expenses*/

        $techExpenses = TechExpense::with('area')->whereBetween('tech_expenses.created_at', [$report->start_date, $report->end_date])
            ->where('tech_expenses.active', 1)
            ->get();

        /* Bordadora */

        $threadUpdateExpenses = ThreadUpdate::whereBetween('thread_updates.created_at', [$report->start_date, $report->end_date])
            ->where('thread_updates.active', 1)
            ->selectRaw('sum(thread_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $threadUpdateDonation = ThreadUpdate::whereBetween('thread_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['thread_updates.purchase_price', 0],
            ['thread_updates.active', 1]
        ])
        ->selectRaw('sum(thread_updates.estimated_value) as donation')
        ->orderBy('donation', 'asc')
        ->get();

        $stabilizerUpdateExpenses = StabilizerUpdate::whereBetween('stabilizer_updates.created_at', [$report->start_date, $report->end_date])
            ->where('stabilizer_updates.active', 1)
            ->selectRaw('sum(stabilizer_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $stabilizerUpdateDonation = StabilizerUpdate::whereBetween('stabilizer_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['stabilizer_updates.purchase_price', 0],
            ['stabilizer_updates.active', 1]
        ])
        ->selectRaw('sum(stabilizer_updates.estimated_value) as donation') 
        ->orderBy('donation', 'asc')
        ->get();
        

        $SUMIncome = $SUMIncome->map(function ($item) use ($threadUpdateExpenses, $stabilizerUpdateExpenses) {
            if ($item->id == 8) {
                $item->egresos = $threadUpdateExpenses[0]->egresos + $stabilizerUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* Cortadora de vinilo */

        $vinylUpdateExpenses = VinylUpdate::whereBetween('vinyl_updates.created_at', [$report->start_date, $report->end_date])
            ->where('vinyl_updates.active', 1)
            ->selectRaw('sum(vinyl_updates.cost) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $vinylUpdateDonation = VinylUpdate::whereBetween('vinyl_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['vinyl_updates.cost', 0],
            ['vinyl_updates.active', 1]
        ])
        ->selectRaw('sum(vinyl_updates.estimated_value) as donation')
        ->orderBy('donation', 'asc')
        ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($vinylUpdateExpenses) {
            if ($item->id == 4) {
                $item->egresos = $vinylUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        /* Electronica */

        $componentsUpdateExpenses = ComponentUpdate::whereBetween('component_updates.created_at', [$report->start_date, $report->end_date])
            ->where('component_updates.active', 1)
            ->selectRaw('sum(component_updates.purchase_price) as egresos, component_updates.quantity')
            ->orderBy('egresos', 'asc')
            ->groupBy('component_updates.component_id', 'component_updates.quantity')
            ->get();

        $componentsUpdateDonation = ComponentUpdate::whereBetween('component_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['component_updates.purchase_price', 0],
            ['component_updates.active', 1]
        ])
        ->selectRaw('sum(component_updates.estimated_value) as donation, component_updates.quantity')
        ->orderBy('donation', 'asc')
        ->groupBy('component_updates.component_id', 'component_updates.quantity')
        ->get();

        /* Group bt component_id, */

        $SUMIncome = $SUMIncome->map(function ($item) use ($componentsUpdateExpenses) {
            if ($item->id == 1) {
                foreach ($componentsUpdateExpenses as $component) {
                    $item->egresos += $component->egresos * $component->quantity;
                }
            }
            return $item;
        });


        /* filaments */

        $filamentsUpdateExpenses = FilamentUpdate::whereBetween('filament_updates.created_at', [$report->start_date, $report->end_date])
            ->where('filament_updates.active', 1)
            ->selectRaw('sum(filament_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($filamentsUpdateExpenses) {
            if ($item->id == 5) {
                $item->egresos = $filamentsUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        $filamentsUpdateDonation = FilamentUpdate::whereBetween('filament_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['filament_updates.purchase_price', 0],
            ['filament_updates.active', 1]
        ])
        ->selectRaw('sum(filament_updates.estimated_value) as donation')
        ->orderBy('donation', 'asc')
        ->get();

        /* resins */
        $resinsUpdateExpenses = ResinUpdate::whereBetween('resin_updates.created_at', [$report->start_date, $report->end_date])
            ->where('resin_updates.active', 1)
            ->selectRaw('sum(resin_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($resinsUpdateExpenses) {
            if ($item->id == 6) {
                $item->egresos = $resinsUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        $resinsUpdateDonation = ResinUpdate::whereBetween('resin_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['resin_updates.purchase_price', 0],
            ['resin_updates.active', 1]
        ])
        ->selectRaw('sum(resin_updates.estimated_value) as donation')
        ->orderBy('donation', 'asc')
        ->get();

        /* Softwares */
        $softwaresExpenses = SoftwareUpdate::whereBetween('software_updates.created_at', [$report->start_date, $report->end_date])
            ->where('software_updates.active', 1)
            ->selectRaw('sum(software_updates.purchase_price) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($softwaresExpenses) {
            if ($item->id == 7) {
                $item->egresos = $softwaresExpenses[0]->egresos;
            }
            return $item;
        });

        $softwaresDonation = SoftwareUpdate::whereBetween('software_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['software_updates.purchase_price', 0],
            ['software_updates.active', 1]
        ])
        ->selectRaw('sum(software_updates.estimated_value) as donation')
        ->orderBy('donation', 'asc')
        ->get();

        /* Materials Laser */

        $materialsLaserUpdateExpenses = LaserUpdate::whereBetween('laser_updates.created_at', [$report->start_date, $report->end_date])
            ->where('laser_updates.active', 1)
            ->selectRaw('sum(laser_updates.cost) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($materialsLaserUpdateExpenses) {
            if ($item->id == 3) {
                $item->egresos = $materialsLaserUpdateExpenses[0]->egresos;
            }
            return $item;
        });

        $materialsLaserUpdateDonation = LaserUpdate::whereBetween('laser_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['laser_updates.cost', 0],
            ['laser_updates.active', 1]
        ])
        ->selectRaw('sum(laser_updates.estimated_value) as donation')
        ->orderBy('donation', 'asc')
        ->get();

        /* Milling */

        $millingUpdateExpenses = MillingUpdate::whereBetween('milling_updates.created_at', [$report->start_date, $report->end_date])
            ->where('milling_updates.active', 1)
            ->selectRaw('sum(milling_updates.purchase_price) as egresos, milling_updates.quantity')
            ->orderBy('egresos', 'asc')
            ->groupBy('milling_updates.material_milling_id', 'milling_updates.quantity')
            ->get();

        /* Donation quantity */

        $millingUpdateDonation = MillingUpdate::whereBetween('milling_updates.created_at', [$report->start_date, $report->end_date])
        ->where([
            ['milling_updates.purchase_price', 0],
            ['milling_updates.active', 1]
        ])
        ->selectRaw('sum(milling_updates.estimated_value) as donation, milling_updates.quantity')
        ->orderBy('donation', 'asc')
        ->groupBy('milling_updates.material_milling_id', 'milling_updates.quantity')
        ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($millingUpdateExpenses) {
            if ($item->id == 2) {
                foreach ($millingUpdateExpenses as $milling) {
                    $item->egresos += $milling->egresos * $milling->quantity;
                }
            }
            return $item;
        });

        /* Donation total */

        $donationTotal = $materialsLaserUpdateDonation[0]->donation + $filamentsUpdateDonation[0]->donation + $resinsUpdateDonation[0]->donation + $softwaresDonation[0]->donation + $vinylUpdateDonation[0]->donation; + $threadUpdateDonation[0]->donation + $stabilizerUpdateDonation[0]->donation;

        foreach ($millingUpdateDonation as $milling) {
            $donationTotal += $milling->donation * $milling->quantity;
        }

        foreach ($componentsUpdateDonation as $component) {
            $donationTotal += $component->donation * $component->quantity;
        }

        $report->end_date = date('Y-m-d', strtotime($report->end_date));

        $type = match ($report->type) {
            'c' => function () use ($report, $totalSalesEvents, $useMachines, $SUMIncome, $totalSalesMaker, $totalSalesServices, $eventsExpensesIncome, $techExpenses, $donationTotal) {
                $pdf = PDF::loadView('inventary', [
                    'report' => $report,
                    'totalSalesEvents' => $totalSalesEvents,
                    'totalSalesSUM' => $useMachines,
                    'SUMIncome' => $SUMIncome,
                    'totalSalesMaker' => $totalSalesMaker,
                    'totalSalesServices' => $totalSalesServices,
                    'eventsExpensesIncome' => $eventsExpensesIncome->sortBy('name')->values()->all(),
                    'techExpenses' => $techExpenses->sortBy('name')->values()->all(),
                    'donationTotal' => $donationTotal,
                ]);
                return $pdf->stream();
            },
            'v' =>  function () use ($report, $totalVisits, $totalTime, $customersTop, $districtsTop, $timeDifferentAreas, $timeDifferentReasonVisit, $ageRangeTotal, $typeSexesTotal, $observations, $totalBookingsD, $totalBookingsC) {
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
                    'totalBookingsD' => $totalBookingsD,
                    'totalBookingsC' => $totalBookingsC,
                ]);
                return $pdf->stream();
            },
            default =>  function () use ($report, $totalVisits, $totalTime, $customersTop, $districtsTop, $timeDifferentAreas, $timeDifferentReasonVisit, $ageRangeTotal, $typeSexesTotal, $observations, $totalBookingsD, $totalBookingsC, $totalSalesEvents, $useMachines, $SUMIncome, $totalSalesMaker, $totalSalesServices, $eventsExpensesIncome, $techExpenses, $donationTotal) {
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
                    'totalBookingsD' => $totalBookingsD,
                    'totalBookingsC' => $totalBookingsC,
                    'totalSalesEvents' => $totalSalesEvents,
                    'totalSalesSUM' => $useMachines,
                    'SUMIncome' => $SUMIncome,
                    'totalSalesMaker' => $totalSalesMaker,
                    'totalSalesServices' => $totalSalesServices,
                    'eventsExpensesIncome' => $eventsExpensesIncome->sortBy('name')->values()->all(),
                    'techExpenses' => $techExpenses->sortBy('name')->values()->all(),
                    'donationTotal' => $donationTotal,
                ]);
                return $pdf->stream();
            }
        };

        return $type();
    }
}
