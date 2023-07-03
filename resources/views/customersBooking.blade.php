<!DOCTYPE html>
<html>
<head>
    <title>Lista de visitantes</title>
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
    @if (count($customersBooking) > 0)
    <div class="head-title">
        <h1 class="text-center m-0 p-0">Lista de visitantes</h1>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Solicitante: <span class="gray-color">{{ $booking->name }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Identificación - <span class="gray-color">{{ $booking->document_number }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Fecha - <span class="gray-color">{{ $booking->date }}</span></p>
        </div>
        <div class="w-50 float-left logo mt-10">
            <img src="http://cloud.flcidete.xyz/images/fab.png">
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Documento</th>
                <th class="w-50">Nombre</th>
                <th class="w-50">Correo</th>
                <th class="w-50">Provincia</th>
            </tr>
            @foreach($customersBooking as $customer)
                <tr>
                    <td class="w-50">{{ $customer->document_number }}</td>
                    <td class="w-50">{{ $customer->name }}</td>
                    <td class="w-50">{{ $customer->email }}</td>
                    <td class="w-50">{{ $customer->province_id }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @else
        <h1 class="text-center m-0 p-0">No hay participantes inscritos</h1>
    @endif
</body>
</html>