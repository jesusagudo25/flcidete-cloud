<!DOCTYPE html>
<html>

<head>
    <title>Factura - #{{ $invoice->id }}</title>
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

    .text-center {
        text-align: center !important;
    }

    .w-100 {
        width: 100%;
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

    .w-10{
        width: 13%;
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
</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">Factura</h1>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Factura - <span class="gray-color">#{{ $invoice->id }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Orden - <span class="gray-color">@php echo Str::random(10);@endphp</span></p>
            <p class="m-0 pt-5 text-bold w-100">Fecha - <span class="gray-color">{{ $invoice->created_at }}</span></p>
        </div>
        <div class="w-50 float-left logo mt-10">
            <img src="https://explorando.xyz/FABLAB/assets/img/fab.png">
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Cliente</th>
                <th class="w-50">Vendedor</th>
            </tr>
            <tr>
                <td>
                    <div class="box-text">
                        <p>{{ $customer->name }}</p>
                        <p>{{ $customer->document_number }}</p>
                        <p>{{ $province_id }}</p>
                        <p>{{ $district_id }}</p>
                        <p>{{ $township_id }}</p>
                        <p>Contácto : {{ $customer->telephone }}</p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p>{{ $user->name }}</p>
                        <p>{{ $user->email }}</p>
                        <p>Veraguas</p>
                        <p>Santiago</p>
                        <p>Canto del llano</p>
                        <p>Contácto : 935-1764</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Método de pago</th>
                <th class="w-50">Tiempo de entrega</th>
            </tr>
            <tr>
                <td>Efectivo en caja</td>
                <td>{{ $invoice->date_delivery }}</td>
            </tr>
        </table>
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Cantidad abonada</th>
                <th class="w-50">Balance</th>
                <th class="w-50">Fecha</th>
            </tr>
            @foreach ($payments as $payment)
            <tr>
                <td>{{ $payment->payment_amount }}</td>
                <td>{{ $payment->balance }}</td>
                <td>{{ $payment->created_at }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-15">#</th>
                <th class="w-50">Descripcion</th>
                <th class="w-15">Total</th>
            </tr>
            <tr>
                <td style="text-align: center">1</td>
                <td>{{ $invoice->description}}</td>
                <td style="text-align: center">${{ $invoice->total }}</td>
            </tr>
            <tr>
                <td colspan="3">
                    @if ($payments->count() > 1)
                    <div class="total-part">
                        <div class="total-left w-85 float-left" align="right">
                            <p>Sub total</p>
                            <p>Abono (50%)</p>
                            <p>Saldo</p>
                            <p>Total a pagar</p>
                        </div>
                        <div class="total-right w-10 float-left text-bold" align="right">
                            <p>{{ $invoice->total }}</p>
                            <p>{{ $payments[1]->payment_amount }}</p>
                            <p>{{ $payments[1]->balance }}</p>
                            <p>{{ $payments[1]->payment_amount }}</p>
                        </div>
                        <div style="clear: both;"></div>
                    </div> 
                    @else
                    <div class="total-part">
                        <div class="total-left w-85 float-left" align="right">
                            <p>Sub total</p>
                            <p>Abono (50%)</p>
                            <p>Saldo</p>
                            <p>Total a pagar</p>
                        </div>
                        <div class="total-right w-10 float-left text-bold" align="right">
                            <p>{{ $invoice->total }}</p>
                            <p>{{ $payments[0]->payment_amount }}</p>
                            <p>{{ $payments[0]->balance }}</p>
                            <p>{{ $payments[0]->payment_amount }}</p>
                        </div>
                        <div style="clear: both;"></div>
                    </div> 
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
