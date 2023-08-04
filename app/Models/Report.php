<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'start_date',
        'end_date',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIncomeAndExpensesByMonth($start_date, $end_date)
    {
        /* ------------------------------- Ingresos ------------------------------- */

        $totalSalesEvents = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->join('event_invoice', 'invoices.id', '=', 'event_invoice.invoice_id')
            ->sum('total');

        $totalSalesSUM = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->join('sums', 'invoices.id', '=', 'sums.invoice_id')
            ->sum('total');

        $totalSalesEmbroidery = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->join('su_embroideries', 'invoices.id', '=', 'su_embroideries.invoice_id')
            ->sum('total');

        $totalSalesLargePrinter = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->join('use_large_printers', 'invoices.id', '=', 'use_large_printers.invoice_id')
            ->sum('total');

        $totalSalesDesignService = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where(
                [
                    ['invoices.status', 'F'],
                    ['invoices.is_active', 1]
                ]
            )
            ->join('design_services', 'invoices.id', '=', 'design_services.invoice_id')
            ->sum('total');

        $useMachines = $totalSalesSUM + $totalSalesEmbroidery + $totalSalesEvents + $totalSalesLargePrinter + $totalSalesDesignService;

        /* ------------------------------- Egresos ------------------------------- */

        /* Eventos */

        $eventsExpenses = Event::whereBetween('created_at', [$start_date, $end_date])
            ->where('active', 1)
            ->sum('expenses');

        /*  Areas  */

        /* Cortadora de vinilo */

        $vinylExpenses = SUMVinyl::whereBetween('invoices.created_at', [$start_date, $end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('vinyls', 'sum_vinyl.vinyl_id', '=', 'vinyls.id')
            ->join('sums', 'sum_vinyl.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->sum(DB::raw('ROUND((sum_vinyl.width * sum_vinyl.height * sums.quantity) * vinyls.purchase_price, 2)'));

        /* Electronica */

        $componentsExpenses = SUMComponent::whereBetween('invoices.created_at', [$start_date, $end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('components', 'sum_component.component_id', '=', 'components.id')
            ->join('sums', 'sum_component.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->sum(DB::raw('ROUND((sum_component.quantity * components.estimated_value) * sums.quantity, 2)'));

        /* Filaments */

        $filamentsExpenses = SUMFilament::whereBetween('invoices.created_at', [$start_date, $end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('filaments', 'sum_filament.filament_id', '=', 'filaments.id')
            ->join('sums', 'sum_filament.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->sum(DB::raw('ROUND((filaments.estimated_value / filaments.purchased_weight) * sum_filament.quantity * sums.quantity, 2)'));

        /* Resins */
        $resinsExpenses = SUMResin::whereBetween('invoices.created_at', [$start_date, $end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('resins', 'sum_resin.resin_id', '=', 'resins.id')
            ->join('sums', 'sum_resin.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->sum(DB::raw('ROUND((resins.estimated_value / resins.purchased_weight) * sum_resin.quantity * sums.quantity, 2)'));

        /* Materials Laser */
        $materialsLaserExpenses = SUMMaterialLaser::whereBetween('invoices.created_at', [$start_date, $end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('material_lasers', 'sum_material_laser.material_laser_id', '=', 'material_lasers.id')
            ->join('sums', 'sum_material_laser.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->sum(DB::raw('ROUND((sum_material_laser.width * sum_material_laser.height * sums.quantity) * material_lasers.purchase_price, 2)'));

        /* Milling */

        $millingExpenses = SUMMaterialMilling::whereBetween('invoices.created_at', [$start_date, $end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('material_millings', 'sum_material_milling.material_milling_id', '=', 'material_millings.id')
            ->join('sums', 'sum_material_milling.sum_id', '=', 'sums.id')
            ->join('invoices', 'sums.invoice_id', '=', 'invoices.id')
            ->sum(DB::raw('ROUND((sum_material_milling.quantity * material_millings.estimated_value) * sums.quantity, 2)'));


        /* Use large printer */

        $largePrinterExpenses = UseLargePrinter::whereBetween('invoices.created_at', [$start_date, $end_date])
            ->where([
                ['invoices.status', 'F'],
                ['invoices.is_active', 1]
            ])
            ->join('printer_materials', 'use_large_printers.printer_material_id', '=', 'printer_materials.id')
            ->join('invoices', 'use_large_printers.invoice_id', '=', 'invoices.id')
            ->sum(DB::raw('ROUND((use_large_printers.width * use_large_printers.height * use_large_printers.quantity) * printer_materials.purchase_price, 2)'));

        return [
            'income' => round($useMachines, 2),
            'expenses' => round($vinylExpenses + $componentsExpenses + $filamentsExpenses + $resinsExpenses + $materialsLaserExpenses + $millingExpenses + $eventsExpenses + $largePrinterExpenses, 2),
        ];
    }
}
