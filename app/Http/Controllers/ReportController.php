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
use App\Models\PrinterMaterialUpdate;
use App\Models\ReasonVisit;
use App\Models\Report;
use App\Models\Resin;
use App\Models\ResinUpdate;
use App\Models\Software;
use App\Models\SoftwareUpdate;
use App\Models\Stabilizer;
use App\Models\StabilizerUpdate;
use App\Models\SUM;
use App\Models\SUMComponent;
use App\Models\SUMFilament;
use App\Models\SUMMaterialLaser;
use App\Models\SUMMaterialMilling;
use App\Models\SUMResin;
use App\Models\SUMVinyl;
use App\Models\TechExpense;
use App\Models\Thread;
use App\Models\ThreadUpdate;
use App\Models\TypeSex;
use App\Models\UseLargePrinter;
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
        /* ------------------------------- Report: Visits ----------------------------------------------------*/
        /* Add 23:60 hours to end_date */

        $report->end_date = Carbon::parse($report->end_date)->addHours(23)->addMinutes(59)->toDateTimeString();

        /* Total de visits */

        $totalVisits = Visit::whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('active', 1)
            ->count();

        /* Tiempo total de cada visita */

        $totalTime = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->selectRaw('sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->get();

        /* ----------------- Los 3 clientes mas frecuentes ----------------- */
        $customersTop = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.name, count(*) as total')
            ->groupBy('customer_visit.customer_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

        /* ----------------- Los 3 distritos mas frecuentes ----------------- */

        $districtsTop = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->selectRaw('customers.district_id, count(*) as total')
            ->groupBy('customers.district_id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

        $districts = Http::get(config('config.geoptyapi') . '/api/districts')->collect();

        foreach ($districtsTop as $districtTop) {
            $result = $districts->where('id', $districtTop->district_id)->first();
            $districtTop->district_id = $result['name'];
        }

        /* ----------------- Áreas de trabajo - horas ----------------- */
        $timeDifferentAreas = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->join('area_visit', 'visits.id', '=', 'area_visit.visit_id')
            ->join('areas', 'area_visit.area_id', '=', 'areas.id')
            ->where([
                ['visits.active', 1],
                ['areas.id', '!=', 8],
                ['areas.id', '!=', 9]
            ])
            ->selectRaw('areas.id, areas.name, sum(hour(TIMEDIFF(area_visit.end_time, area_visit.start_time))) as total')
            ->groupBy('area_visit.area_id')
            ->orderBy('total', 'desc')
            ->get();

        $areasNotShow = Area::whereNotIn('id', $timeDifferentAreas->pluck('id'))->whereNotIn('id', [8, 9])->select('id', 'name')->get();
        $timeDifferentAreas = $timeDifferentAreas->merge($areasNotShow);

        /* ----------------- Razones de visita - horas ----------------- */

        $visitsRS = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->get();

        //map
        $visitsRS->map(function ($visit) {
            //get first area of visit with start_time and end_time
            $area = $visit->areas->first();
            $start_time = Carbon::parse($area->pivot->start_time);
            $end_time = Carbon::parse($area->pivot->end_time);
            $visit->totalTime = $start_time->diffInHours($end_time);

            //search areas with different start_time and end_time
            $areas = $visit->areas->where('pivot.start_time', '!=', $area->pivot->start_time)->where('pivot.end_time', '!=', $area->pivot->end_time);

            // add total time of areas with different start_time and end_time
            foreach ($areas as $area) {
                $start_time = Carbon::parse($area->pivot->start_time);
                $end_time = Carbon::parse($area->pivot->end_time);
                $visit->totalTime += $start_time->diffInHours($end_time);
            }
            return $visit;
        });

        //get reason visit name
        $visitsRS->map(function ($visit) {
            $visit->reason = $visit->reasonVisit;
            return $visit;
        });

        //group by reason visit
        //return collection with reason visit name and total time
        $timeDifferentReasonVisit = $visitsRS->groupBy('reason.name');

        //array with id, reason visit name and total time
        $timeDifferentReasonVisit = $timeDifferentReasonVisit->map(function ($item, $key) {
            return [
                'id' => $item->first()->reason->id,
                'name' => $key,
                'total' => $item->sum('totalTime')
            ];
        });

        //to collect
        $timeDifferentReasonVisit = collect($timeDifferentReasonVisit->values());

        $reasonVisitsNotShow = ReasonVisit::whereNotIn('id', $timeDifferentReasonVisit->pluck('id'))->select('id', 'name')->get();
        $timeDifferentReasonVisit = $timeDifferentReasonVisit->merge($reasonVisitsNotShow);

        $timeTotalReason = $timeDifferentReasonVisit->sum('total');

        /* ----------------- Rango de edades ----------------- */

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

        /* ----------------- Sexo ----------------- */

        $typeSexesTotal = Visit::whereBetween('visits.created_at', [$report->start_date, $report->end_date])
            ->where('visits.active', 1)
            ->join('customer_visit', 'visits.id', '=', 'customer_visit.visit_id')
            ->join('customers', 'customer_visit.customer_id', '=', 'customers.id')
            ->join('type_sexes', 'customers.type_sex_id', '=', 'type_sexes.id')
            ->selectRaw('type_sexes.id, type_sexes.name, count(*) as total')
            ->groupBy('customers.type_sex_id')
            ->orderBy('total', 'desc')
            ->get();

        $typeSexesNotShow = TypeSex::whereNotIn('id', $typeSexesTotal->pluck('id'))->whereNotIn('id', [3])->select('id', 'name')->get();

        $typeSexesTotal = $typeSexesTotal->merge($typeSexesNotShow);

        $observations = Observation::with('user')->whereBetween('created_at', [$report->start_date, $report->end_date])
            ->where('active', 1)
            ->get();

        /* ------------------------------- Report: Expenses and income ------------------------------- */

        /* Total de ventas por clientes con documento 'C' | 'P' */
        $SalesPerPerson = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->whereIn('customers.document_type', ['C', 'P'])
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->selectRaw('count(invoices.total) as total')
            ->first();


        /* Total de ventas por clientes con documento R */
        $SalesByCompany = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->where('customers.document_type', 'R')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->selectRaw('count(invoices.total) as total')
            ->first();

        /* ------------------------------- Ingresos ------------------------------- */

        /* Ingresos por eventos */
        $eventsExpensesIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->join('event_invoice', 'invoices.id', '=', 'event_invoice.invoice_id')
            ->join('events', 'event_invoice.event_id', '=', 'events.id')
            ->selectRaw('events.name, events.price, events.expenses AS egresos, event_invoice.event_id, sum(events.price) as ingresos')
            ->groupBy('events.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        /* Ingresos en areas */

        $SUMIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->join('sums', 'invoices.id', '=', 'sums.invoice_id')
            ->join('areas', 'sums.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(sums.base_cost * sums.quantity) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SUEIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('su_embroideries', 'invoices.id', '=', 'su_embroideries.invoice_id')
            ->join('areas', 'su_embroideries.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(su_embroideries.base_cost * su_embroideries.quantity) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SULargePrinterIncome = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('use_large_printers', 'invoices.id', '=', 'use_large_printers.invoice_id')
            ->join('areas', 'use_large_printers.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(use_large_printers.base_cost * use_large_printers.quantity) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();

        $SUDesign = Invoice::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('design_services', 'invoices.id', '=', 'design_services.invoice_id')
            ->join('areas', 'design_services.area_id', '=', 'areas.id')
            ->selectRaw('areas.id, areas.name, sum(design_services.base_cost * design_services.quantity) as ingresos')
            ->groupBy('areas.id')
            ->orderBy('ingresos', 'asc')
            ->get();


        $SUMIncome = $SUMIncome->merge($SUEIncome);

        $SUMIncome = $SUMIncome->merge($SULargePrinterIncome);

        $SUMIncome = $SUMIncome->merge($SUDesign);

        $SUMIncomeNotShow = Area::whereNotIn('id', $SUMIncome->pluck('id'))->select('id', 'name')->get();

        $SUMIncome = $SUMIncome->merge($SUMIncomeNotShow);

        /* ------------------------------- Tech expenses ------------------------------- */

        $techExpenses = TechExpense::with('area')->whereBetween('tech_expenses.created_at', [$report->start_date, $report->end_date])
            ->where('tech_expenses.active', 1)
            ->get();

        /* ------------------------------- Expenses ------------------------------- */

        /* Cortadora de vinilo */

        $vinylExpenses = SUMVinyl::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('vinyls', 'sum_vinyl.vinyl_id', '=', 'vinyls.id')
            ->join('sums', 'sum_vinyl.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->selectRaw('ROUND(sum((sum_vinyl.width * sum_vinyl.height * sums.quantity) * vinyls.purchase_price), 2) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($vinylExpenses) {
            if ($item->id == 4) {
                $item->egresos = $vinylExpenses[0]->egresos;
            }
            return $item;
        });

        /* Electronica */

        $componentsExpenses = SUMComponent::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('components', 'sum_component.component_id', '=', 'components.id')
            ->join('sums', 'sum_component.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->selectRaw('ROUND(sum((sum_component.quantity * components.estimated_value) * sums.quantity), 2) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($componentsExpenses) {
            if ($item->id == 1) {
                foreach ($componentsExpenses as $component) {
                    $item->egresos += $component->egresos;
                }
            }
            return $item;
        });

        /* filaments */

        $filamentsExpenses = SUMFilament::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('filaments', 'sum_filament.filament_id', '=', 'filaments.id')
            ->join('sums', 'sum_filament.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->selectRaw('ROUND(sum((filaments.estimated_value / filaments.purchased_weight) * sum_filament.quantity * sums.quantity), 2) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($filamentsExpenses) {
            if ($item->id == 5) {
                $item->egresos = $filamentsExpenses[0]->egresos;
            }
            return $item;
        });

        /* resins */

        $resinsExpenses = SUMResin::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('resins', 'sum_resin.resin_id', '=', 'resins.id')
            ->join('sums', 'sum_resin.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->selectRaw('ROUND(sum((resins.estimated_value / resins.purchased_weight) * sum_resin.quantity * sums.quantity), 2) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($resinsExpenses) {
            if ($item->id == 6) {
                $item->egresos = $resinsExpenses[0]->egresos;
            }
            return $item;
        });

        /* Materials Laser */

        $materialsLaserExpenses = SUMMaterialLaser::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('material_lasers', 'sum_material_laser.material_laser_id', '=', 'material_lasers.id')
            ->join('sums', 'sum_material_laser.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->selectRaw('ROUND(sum((sum_material_laser.width * sum_material_laser.height * sums.quantity) * material_lasers.purchase_price), 2) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();


        $SUMIncome = $SUMIncome->map(function ($item) use ($materialsLaserExpenses) {
            if ($item->id == 3) {
                $item->egresos = $materialsLaserExpenses[0]->egresos;
            }
            return $item;
        });

        /* Milling */

        $millingExpenses = SUMMaterialMilling::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('material_millings', 'sum_material_milling.material_milling_id', '=', 'material_millings.id')
            ->join('sums', 'sum_material_milling.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->selectRaw('ROUND(sum((sum_material_milling.quantity * material_millings.estimated_value) * sums.quantity), 2) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($millingExpenses) {
            if ($item->id == 2) {
                foreach ($millingExpenses as $milling) {
                    $item->egresos += $milling->egresos;
                }
            }
            return $item;
        });

        /* Use large printer */

        $largePrinterExpenses = UseLargePrinter::whereBetween('invoices.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('printer_materials', 'use_large_printers.printer_material_id', '=', 'printer_materials.id')
            ->join('invoices', 'use_large_printers.invoice_id', '=', 'invoices.id')
            ->selectRaw('ROUND(sum((use_large_printers.width * use_large_printers.height * use_large_printers.quantity) * printer_materials.purchase_price), 2) as egresos')
            ->orderBy('egresos', 'asc')
            ->get();

        $SUMIncome = $SUMIncome->map(function ($item) use ($largePrinterExpenses) {
            if ($item->id == 8) {
                $item->egresos = $largePrinterExpenses[0]->egresos;
            }
            return $item;
        });

        /* ----------------------------------------- Donation ----------------------------------------- */

        $vinylDonation = VinylUpdate::whereBetween('vinyl_updates.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['vinyl_updates.cost', 0],
                ['vinyl_updates.active', 1]
            ])
            ->selectRaw('sum(vinyl_updates.estimated_value) as donation')
            ->orderBy('donation', 'asc')
            ->get();

        $componentsDonation = ComponentUpdate::whereBetween('component_updates.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['component_updates.purchase_price', 0],
                ['component_updates.active', 1]
            ])
            ->selectRaw('sum(component_updates.estimated_value) as donation, component_updates.quantity')
            ->orderBy('donation', 'asc')
            ->groupBy('component_updates.component_id', 'component_updates.quantity')
            ->get();

        $filamentsDonation = FilamentUpdate::whereBetween('filament_updates.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['filament_updates.purchase_price', 0],
                ['filament_updates.active', 1]
            ])
            ->selectRaw('sum(filament_updates.estimated_value) as donation')
            ->orderBy('donation', 'asc')
            ->get();

        $resinsDonation = ResinUpdate::whereBetween('resin_updates.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['resin_updates.purchase_price', 0],
                ['resin_updates.active', 1]
            ])
            ->selectRaw('sum(resin_updates.estimated_value) as donation')
            ->orderBy('donation', 'asc')
            ->get();

        $materialsLaserDonation = LaserUpdate::whereBetween('laser_updates.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['laser_updates.cost', 0],
                ['laser_updates.active', 1]
            ])
            ->selectRaw('sum(laser_updates.estimated_value) as donation')
            ->orderBy('donation', 'asc')
            ->get();

        $millingDonation = MillingUpdate::whereBetween('milling_updates.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['milling_updates.purchase_price', 0],
                ['milling_updates.active', 1]
            ])
            ->selectRaw('sum(milling_updates.estimated_value) as donation, milling_updates.quantity')
            ->orderBy('donation', 'asc')
            ->groupBy('milling_updates.material_milling_id', 'milling_updates.quantity')
            ->get();

        $printerMaterialsDonation = PrinterMaterialUpdate::whereBetween('printer_material_updates.created_at', [$report->start_date, $report->end_date])
            ->where([
                ['printer_material_updates.cost', 0],
                ['printer_material_updates.active', 1]
            ])
            ->selectRaw('sum(printer_material_updates.estimated_value) as donation')
            ->orderBy('donation', 'asc')
            ->get();

        $donationTotal = $materialsLaserDonation[0]->donation + $filamentsDonation[0]->donation + $resinsDonation[0]->donation + $vinylDonation[0]->donation + $printerMaterialsDonation[0]->donation;

        foreach ($millingDonation as $milling) {
            $donationTotal += $milling->donation * $milling->quantity;
        }

        foreach ($componentsDonation as $component) {
            $donationTotal += $component->donation * $component->quantity;
        }

        /* --------------------------- Generate PDF --------------------------- */

        $report->end_date = date('Y-m-d', strtotime($report->end_date));

        $type = match ($report->type) {
            'c' => function () use ($report, $SalesPerPerson, $SalesByCompany, $SUMIncome, $eventsExpensesIncome, $techExpenses, $donationTotal) {
                $pdf = PDF::loadView('income-expenses', [
                    'report' => $report,
                    'SalesPerPerson' => $SalesPerPerson->total,
                    'SalesByCompany' => $SalesByCompany->total,
                    'SUMIncome' => $SUMIncome,
                    'eventsExpensesIncome' => $eventsExpensesIncome->sortBy('name')->values()->all(),
                    'techExpenses' => $techExpenses->sortBy('name')->values()->all(),
                    'donationTotal' => $donationTotal,
                ]);
                return $pdf->stream();
            },
            'v' =>  function () use ($report, $totalVisits, $totalTime, $timeTotalReason, $customersTop, $districtsTop, $timeDifferentAreas, $timeDifferentReasonVisit, $ageRangeTotal, $typeSexesTotal, $observations) {
                $pdf = PDF::loadView('visits', [
                    'report' => $report,
                    'totalVisits' => $totalVisits,
                    'totalTime' => $totalTime[0]->total,
                    'timeTotalReason' => $timeTotalReason,
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
            default =>  function () use ($report, $totalVisits, $totalTime, $timeTotalReason, $customersTop, $districtsTop, $timeDifferentAreas, $timeDifferentReasonVisit, $ageRangeTotal, $typeSexesTotal, $observations, $SalesPerPerson, $SalesByCompany, $SUMIncome, $eventsExpensesIncome, $techExpenses, $donationTotal) {
                $pdf = PDF::loadView('general', [
                    'report' => $report,
                    'totalVisits' => $totalVisits,
                    'totalTime' => $totalTime[0]->total,
                    'timeTotalReason' => $timeTotalReason,
                    'customersTop' => $customersTop,
                    'districtsTop' => $districtsTop,
                    'timeDifferentAreas' => $timeDifferentAreas->sortBy('name')->values()->all(),
                    'timeDifferentReasonVisit' => $timeDifferentReasonVisit->sortBy('name')->values()->all(),
                    'ageRangeTotal' => $ageRangeTotal->sortBy('name')->values()->all(),
                    'typeSexesTotal' => $typeSexesTotal->sortBy('name')->values()->all(),
                    'observations' => $observations,

                    'SalesPerPerson' => $SalesPerPerson->total,
                    'SalesByCompany' => $SalesByCompany->total,
                    'SUMIncome' => $SUMIncome,
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
