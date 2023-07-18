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

    public function getIncomeAndExpensesByMonth($start_date, $end_date){
        /* Informacion basica */

        $totalSalesEvents = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where('status','F')
            ->join('event_invoice', 'invoices.id', '=', 'event_invoice.invoice_id')
            ->sum('total');

        $totalSalesSUM = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where('status','F')
            ->join('sums', 'invoices.id', '=', 'sums.invoice_id')
            ->sum('total');

        $totalSalesEmbroidery = Invoice::whereBetween('created_at', [$start_date, $end_date])
            ->where('status','F')
            ->join('su_embroideries', 'invoices.id', '=', 'su_embroideries.invoice_id')
            ->sum('total');

        $useMachines = $totalSalesSUM + $totalSalesEmbroidery + $totalSalesEvents;

        /* Ingresos y gastos en eventos */

        $eventsExpenses = Event::whereBetween('created_at', [$start_date, $end_date])
        ->where('active', 1)
        ->sum('expenses');


        /* Egresos en areas ----------------------- */

        /* Tech expenses - name and expenses*/

        $techExpenses = TechExpense::with('area')->whereBetween('tech_expenses.created_at', [$start_date, $end_date])
            ->where('tech_expenses.active', 1)
            ->sum('tech_expenses.amount');

        /* Cortadora de vinilo */

        $vinylUpdateExpenses = VinylUpdate::whereBetween('vinyl_updates.created_at', [$start_date,$end_date])
            ->where('vinyl_updates.active', 1)
            ->sum('vinyl_updates.cost');

        /* Electronica */

        $componentsUpdateExpenses = ComponentUpdate::whereBetween('component_updates.created_at', [$start_date, $end_date])
            ->where('component_updates.active', 1)
            ->sum(DB::raw('component_updates.purchase_price * component_updates.quantity'));

        /* filaments */

        $filamentsUpdateExpenses = FilamentUpdate::whereBetween('filament_updates.created_at', [$start_date, $end_date])
            ->where('filament_updates.active', 1)
            ->sum('filament_updates.purchase_price');

        /* resins */
        $resinsUpdateExpenses = ResinUpdate::whereBetween('resin_updates.created_at', [$start_date, $end_date])
            ->where('resin_updates.active', 1)
            ->sum('resin_updates.purchase_price');

        /* Materials Laser */
        $materialsLaserUpdateExpenses = LaserUpdate::whereBetween('laser_updates.created_at', [$start_date, $end_date])
            ->where('laser_updates.active', 1)
            ->sum('laser_updates.cost');
            
        /* Milling */

        $millingUpdateExpenses = MillingUpdate::whereBetween('milling_updates.created_at', [$start_date,$end_date])
            ->where('milling_updates.active', 1)
            ->sum(DB::raw('milling_updates.purchase_price * milling_updates.quantity'));

            return [
                'income' => round($useMachines,2),
                'expenses' => round($techExpenses + $vinylUpdateExpenses + $componentsUpdateExpenses + $filamentsUpdateExpenses + $resinsUpdateExpenses + $materialsLaserUpdateExpenses + $millingUpdateExpenses + $eventsExpenses, 2),
            ];
    }
}
