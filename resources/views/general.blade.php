<!DOCTYPE html>
<html>

<head>
    <title>Informe mensual de {{ $report->month }}</title>
</head>
<style type="text/css">
    body {
        font-family: 'Roboto Condensed', sans-serif;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .pt-5 {
        padding-top: 5px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .mt-30 {
        margin-top: 30px;
    }

    .text-center {
        text-align: center !important;
    }

    .w-100 {
        width: 100%;
    }


    .w-48 {
        width: 48%;
    }


    .w-50 {
        width: 50%;
    }

    .w-85 {
        width: 85%;
    }

    .w-15 {
        width: 15%;
    }

    .logo img {
        width: 55%;
    }

    .logo span {
        margin-left: 8px;
        top: 19px;
        position: absolute;
        font-weight: bold;
        font-size: 25px;
    }

    .gray-color {
        color: #5D5D5D;
    }

    .text-bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }

    table tr,
    th,
    td {
        border: 1px solid #d2d2d2;
        border-collapse: collapse;
        padding: 7px 8px;
    }

    table tr th {
        background: #F4F4F4;
        font-size: 15px;
    }

    table tr td {
        font-size: 13px;
    }

    table {
        border-collapse: collapse;
    }

    .box-text p {
        line-height: 10px;
    }

    .float-left {
        float: left;
    }

    .total-part {
        font-size: 16px;
        line-height: 12px;
    }

    .total-right p {
        padding-right: 20px;
    }

    .page_break {
        page-break-before: always;
    }
</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">Reporte</h1>
        <p style="text-align: center">Informe mensual de {{ $report->month }}</p>
    </div>
    <div class="add-detail">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Reporte - <span class="gray-color">#{{ $report->id }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Tipo - <span class="gray-color">Visitas y Reservaciones</span></p>
            <p class="m-0 pt-5 text-bold w-100">Usuario - <span class="gray-color">{{ $report->user->name }}</span>
            </p>

        </div>
        <div class="w-50 float-left logo mt-10">
            <img src="http://cloud.flcidete.xyz/images/fab.png">
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Fecha de inicio</th>
                <th class="w-50">Fecha de finalización</th>
            </tr>
            <tr>
                <td align="center">{{ $report->start_date }}</td>
                <td align="center">{{ $report->end_date }}</td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-48 mt-10" style="float: left">
            <tr>
                <th class="w-50">Clientes frecuentes</th>
                <th class="w-15">Total</th>
            </tr>
            @if ($customersTop->count() == 0)
                <tr align="center">
                    <td colspan="2" >No hay datos</td>
                </tr>
            @else
                @foreach ($customersTop as $customer)
                    <tr align="center">
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->total }}</td>
                    </tr>
                @endforeach
            @endif
        </table>

        <table class="table w-48 mt-10" style="float: right">
            <tr>
                <th class="w-50">Distritos frecuentes</th>
                <th class="w-15">Total</th>
            </tr>
            @if ($districtsTop->count() == 0)
                <tr align="center">
                    <td colspan="2" >No hay datos</td>
                </tr>
            @else
                @foreach ($districtsTop as $district)
                    <tr align="center">
                        <td>{{ $district->district_id }}</td>
                        <td>{{ $district->total }}</td>
                    </tr>
                @endforeach
            @endif
        </table>

        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Áreas de trabajo</th>
                <th class="w-15">Horas</th>
                <th class="w-15">% del total</th>
            </tr>
            @php $totalT = 0; @endphp
            @foreach ($timeDifferentAreas as $area)
                <tr align="center">
                    <td>{{ $area->name }}</td>
                    <td>{{ $area->total ? $area->total : 0 }}</td>
                    <td>
                        {{ $area->total ? round(($area->total / $totalTime) * 100, 2) : 0 }}%</td>
                    }}</td>
                </tr>
                @php $totalT += $area->total; @endphp
            @endforeach
            <tr align="center">
                <th><strong>Total</strong></th>
                <th><strong>{{ $totalT }}</strong></th>
                <th><strong>{{ $totalTime ? round(($totalT / $totalTime) * 100, 2) : 0 }}%</strong></th>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Razón de visita</th>
                <th class="w-15">Horas</th>
                <th class="w-15">% del total</th>
            </tr>
            @php $totalTR = 0; @endphp
            @foreach ($timeDifferentReasonVisit as $reason)
                <tr align="center">
                    <td>{{ $reason['name'] }}</td>
                    <td>{{ $reason['total'] ? $reason['total'] : 0 }}</td>
                    <td>
                        {{ $reason['total'] ? round(($reason['total'] / $timeTotalReason) * 100, 2) : 0 }}%</td>
                    }}</td>
                </tr>
                @php $totalTR += $reason['total']; @endphp
            @endforeach
            <tr align="center">
                <th><strong>Total</strong></th>
                <th><strong>{{ $totalTR }}</strong></th>
                <th><strong>{{ $timeTotalReason ? round(($totalTR / $timeTotalReason) * 100, 2) : 0 }}%</strong></th>
            </tr>
        </table>
    </div>

    {{-- Page new --}}
    <div class="page_break"></div>

    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-48 mt-10" style="float: left">
            <tr>
                <th class="w-50">Rangos de edad</th>
                <th class="w-15">Total</th>
            </tr>
            @foreach ($ageRangeTotal as $ageRange)
                <tr align="center">
                    <td>{{ $ageRange->name }}</td>
                    <td>{{ $ageRange->total ? $ageRange->total : 0 }}</td>
                </tr>
            @endforeach
        </table>

        <table class="table w-48 mt-10" style="float: right">
            <tr>
                <th class="w-50">Generos</th>
                <th class="w-15">Total</th>
            </tr>
            @foreach ($typeSexesTotal as $gender)
                <tr align="center">
                    <td>{{ $gender->name }}</td>
                    <td>{{ $gender->total ? $gender->total : 0 }}</td>
                </tr>
            @endforeach
        </table>

        <div style="clear: both;"></div>
    </div>

    <div style="clear: both;"></div>


    @if ($observations->count() > 0)
        <div class="table-section bill-tbl w-100 mt-30">
            <div>
                <p class="m-0 pt-5 text-bold w-100">Observaciones</span>
                </p>
                <ol>

                    @foreach ($observations as $observation)
                        <li>{{ $observation->title }} - {{ $observation->description }} -
                            {{ $observation->created_at }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
    @else
        <div class="table-section bill-tbl w-100 mt-30">
            <div>
                <p class="m-0 pt-5 text-bold w-100">Observaciones</span>
                </p>
                <ol>
                    <li>No hay observaciones</li>
                </ol>
            </div>
        </div>
    @endif

    {{-- Page new --}}
    <div class="page_break"></div>

    <div class="head-title">
        <h1 class="text-center m-0 p-0">Reporte</h1>
        <p style="text-align: center">Informe mensual de {{ $report->month }}</p>
    </div>
    <div class="add-detail">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Reporte - <span class="gray-color">#{{ $report->id }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Tipo - <span class="gray-color">Ingresos y egresos</span></p>
            <p class="m-0 pt-5 text-bold w-100">Usuario - <span class="gray-color">{{ $report->user->name }}</span>
            </p>

        </div>
        <div class="w-50 float-left logo mt-10">
            <img src="http://cloud.flcidete.xyz/images/fab.png">
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Fecha de inicio</th>
                <th class="w-50">Fecha de finalización</th>
            </tr>
            <tr>
                <td align="center">{{ $report->start_date }}</td>
                <td align="center">{{ $report->end_date }}</td>
            </tr>
        </table>
    </div>

    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50" colspan="2">Ventas por tipo de cliente</th>
            <tr>
                <th class="w-50">Personas</th>
                <th class="w-50">Empresas/Instituciones</th>
            </tr>
            <tr>
                <td align="center">{{ $SalesPerPerson }}</td>
                <td align="center">{{ $SalesByCompany }}</td>
            </tr>
        </table>
    </div>

    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Áreas de trabajo</th>
                <th class="w-15">Ingresos</th>
                <th class="w-15">Egresos</th>
                <th class="w-15">Diferencia</th>
            </tr>
            @php($incomeSUM = 0.0)
            @php($expensesSUM = 0.0)
            @php($totalSUM = 0.0)
            @foreach ($SUMIncome as $item)
                <tr align="center">
                    <td>{{ $item['name'] }}</td>
                    <td>$ {{ number_format($item['ingresos'] ? $item['ingresos'] : 0.0, 2) }}</td>
                    @php($incomeSUM += $item['ingresos'])
                    <td>$ {{ number_format($item['egresos'] ? $item['egresos'] : 0.0, 2) }}</td>
                    @php($expensesSUM += $item['egresos'])
                    <td>$ {{ number_format($item['ingresos'] - $item['egresos'], 2) }}</td>
                    @php($totalSUM += $item['ingresos'] - $item['egresos'])
                </tr>
            @endforeach
            <tr>
                <th class="w-50">Total</th>
                <th class="w-15">$ {{ number_format($incomeSUM, 2) }}</th>
                <th class="w-15">$ {{ number_format($expensesSUM, 2) }}</th>
                <th class="w-15">$ {{ number_format($totalSUM, 2) }}</th>
            </tr>
        </table>
    </div>

    @php($incomeEvents = 0.0)
    @php($expensesEvents = 0.0)
    @php($totalEvents = 0.0)
    {{-- Page new --}}
    <div class="page_break"></div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Eventos</th>
                <th class="w-15">Ingresos</th>
                <th class="w-15">Egresos</th>
                <th class="w-15">Total</th>
            </tr>
            @if ($eventsExpensesIncome)
                @foreach ($eventsExpensesIncome as $event)
                    <tr align="center">
                        <td>{{ $event['name'] }}</td>
                        <td>$ {{ number_format($event['ingresos'] ? $event['ingresos'] : 0.0, 2) }}</td>
                        @php($incomeEvents += $event['ingresos'])
                        <td>$ {{ number_format($event['egresos'] ? $event['egresos'] : 0.0, 2) }}</td>
                        @php($expensesEvents += $event['egresos'])
                        <td>$ {{ number_format($event['ingresos'] - $event['egresos'], 2) }}</td>
                        @php($totalEvents += $event['ingresos'] - $event['egresos'])
                    </tr>
                @endforeach
            @else
                <tr align="center">
                    <td colspan="4" style="font-size: 15px">No hay eventos</td>
                </tr>
            @endif
            <tr>
                <th class="w-50">Total</th>
                <th class="w-15">$ {{ number_format($incomeEvents, 2) }}</th>
                <th class="w-15">$ {{ number_format($expensesEvents, 2) }}</th>
                <th class="w-15">$ {{ number_format($totalEvents, 2) }}</th>
            </tr>
        </table>
    </div>
    {{-- Page new --}}
    <div class="page_break"></div>

    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Gastos técnicos</th>
                <th class="w-50">Área</th>
                <th class="w-15">Cantidad</th>
            </tr>
            @php($totalTechExpenses = 0)
            @if ($techExpenses)
                @foreach ($techExpenses as $expense)
                    <tr align="center">
                        <td>{{ $expense['name'] }}</td>
                        <td>{{ $expense['area']['name'] }}</td>
                        <td>$ {{ number_format($expense['amount'], 2) }}</td>
                        @php($totalTechExpenses += $expense['amount'])
                    </tr>
                @endforeach
            @else
                <tr align="center">
                    <td colspan="3" style="font-size: 15px">No hay gastos técnicos</td>
                </tr>
            @endif
            <tr>
                <th class="w-50" style="text-align: right" colspan="2">Total</th>
                <th class="w-15">$ {{ number_format($totalTechExpenses, 2) }}</th>
            </tr>
        </table>
    </div>

    {{-- Page new --}}
    <div class="page_break"></div>

    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-15">Total de Ingresos</th>
                <th class="w-15">Total de Egresos</th>
                <th class="w-15">Total de diferencia</th>
            </tr>
            <tr align="center">
                <td>$ {{ number_format($incomeSUM + $incomeEvents, 2) }}</td>
                <td>$ {{ number_format($expensesSUM + $expensesEvents, 2) }}</td>
                <td>$ {{ number_format($totalSUM + $totalEvents, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-15">Donaciones totales</th>
                <td class="w-15" align="center">$ {{ number_format($donationTotal, 2) }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
