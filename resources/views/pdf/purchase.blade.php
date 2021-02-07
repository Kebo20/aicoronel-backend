<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
        <style >
            @page {
                margin: 0cm 0cm;
            }
            body{
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    margin-top: 1cm;
                    margin-left: 2cm;
                    margin-right: 2cm;
                    margin-bottom: 0.5cm;
            }
            /* Define the header rules */
            header {
                position: fixed;
                top: 0cm;
                margin-left: 2cm;
                margin-right: 2cm;
                height: 1cm;

                /* Extra personal styles */
                color: white;
                text-align: center;
                line-height: 1.5cm;
            }

            /* Define the footer rules */
            footer {
                position: fixed;
                bottom: 0cm;
                left: 0cm;
                right: 0cm;
                height: 0.5cm;

                /* Extra personal styles */
                text-align: center;
                line-height: 1cm;
            }

            .center{
                text-align: center;
            }
            .table_assistance{
                border:0px;
                width: 100%;
                margin-bottom: 20px;
                border-collapse: collapse;
                border-radius:10px;
            }
            .table_assistance tr td, .table_assistance tr th {
                padding:4px;
                border: 1px solid #8b8d8d;
                text-align:center;
                font-size:11px;
                border-radius:10px;
            }
            .table_header{
                border:0;
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #6c7070;
            }
            .table_header, .table_header td, .table_header th {
                padding:0!important;
                text-align:center;
                color:black;
                border: 1px solid #6c7070;
            }
            .subtitle{
                background:#d2d2d2;
                padding-top:5px;
                padding-bottom:5px;
                padding-left:5px;
            }
        </style>
    </head>
    <body>
        <header>

        </header>

        <footer>
        </footer>

        <main>
            @if($data['purchase']->status == 0)
                <style>
                    body:after {
                        content: "ANULADA";
                        font-size: 10em;
                        color: rgba(176, 179, 180, 0.4);
                        z-index: 9999;

                        display: flex;
                        align-items: center;
                        justify-content: center;
                        position: fixed;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        /*left: 0;*/

                        left: 180px;
                        font-size: 135px;
                        width: 250px;
                        position: relative;
                        top: 150px;

                        -webkit-transform: rotate(-45deg);
                        -moz-transform: rotate(-45deg);
                        -ms-transform: rotate(-45deg);
                        -o-transform: rotate(-45deg);
                        transform: rotate(-45deg);

                        -webkit-transform-origin: 50% 50%;
                        -moz-transform-origin: 50% 50%;
                        -ms-transform-origin: 50% 50%;
                        -o-transform-origin: 50% 50%;
                        transform-origin: 50% 50%;
                    }
                </style>
            @endif
            <div style="width: 100%">
               
                <!-- <div style="float: left;position: relative;width:70%">
                    <img src="images/aicoronel.jpg" width="60" height="80px" alt="">
                </div> -->
                
                <div style="background-color:#fcfcfc;border-radius: 16px; border: 0.03cm solid black;float: right;position: relative;width: 35%;text-align: center">
                    <b><span style="font-size: 15px">AGRO IMPORT CORONEL</span></b><br>
                    <span style="font-size: 12px"> RUC: 10710988833</span>
                    <div style="background-color:black;width:100%;padding:0.05cm">
                        <span style="font-size: 12px;color:white">N° DOC: {{$data['purchase']->number_doc}}</span>
                    </div>
                    <span style="font-size: 12px">Celular: 990233458</span>
                </div>
            </div>

    <br><br><br><br><br><br><br><br><br>
            <div>
            <table  class="table_assistance" style="">
                <tr>
                    <td colspan="2" rowspan="1" style="text-align:center; background:#1b1b1c;color:white">
                        <strong>Datos de la Compra</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="1" rowspan="1" style="text-align:left; width:100px!important;font-weight:bold;background: #dbdedf">
                        DÍA
                    </td>
                    <td  colspan="1" rowspan="1" style="text-align:left;">{{date('d/m/Y', strtotime($data['purchase']->date))}}</td>
                </tr>
                <tr>
                    <td colspan="1" rowspan="1" style="text-align:left; width:100px!important;font-weight:bold;background: #dbdedf">
                        TIPO DE DOCUMENTO
                    </td>
                    <td  colspan="1" rowspan="1" style="text-align:left;">{{$data['purchase']->type_doc}}</td>
                </tr>
                <tr>
                    <td colspan="1" rowspan="1" style="text-align:left; width:100px!important;font-weight:bold;background: #dbdedf">
                        NÚMERO DE DOCUMENTO
                    </td>
                    <td  colspan="1" rowspan="1" style="text-align:left;">{{$data['purchase']->number_doc}}</td>
                </tr>
                <tr>
                    <td colspan="1" rowspan="1" style="text-align:left; width:100px!important;font-weight:bold;background: #dbdedf">
                        OBSERVACIÓN
                    </td>
                    <td  colspan="1" rowspan="1" style="text-align:left;">{{$data['purchase']->observation}}</td>
                </tr>
                <tr>
                    <td colspan="1" rowspan="1" style="text-align:left; width:100px!important;font-weight:bold;background: #dbdedf">
                        PROVEEDOR
                    </td>
                    <td  colspan="1" rowspan="1" style="text-align:left;">{{$data['purchase']->provider->name}}</td>
                </tr>
                <tr>
                    <td colspan="1" rowspan="1" style="text-align:left; width:100px!important;font-weight:bold;background: #dbdedf">
                        SUCURSAL
                    </td>
                    <td  colspan="1" rowspan="1" style="text-align:left;">{{$data['purchase']->storage->name}}</td>
                </tr>
            </table>
            <table class="table_assistance">
                <thead>
                    <tr>
                        <th style="background:#1b1b1c; text-align:center; color:white">PRODUCTO</th>
                        <th style="background:#1b1b1c; text-align:center; color:white">CANT</th>
                        <th style="background:#1b1b1c; text-align:center; color:white">PREC</th>
                        <th style="background:#1b1b1c; text-align:center; color:white">SUBT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['detail'] as $key => $detail)
                    @if($key%2==0)
                        <tr style="background: #dbdedf">
                            <td colspan="1" style="text-align:center;">{{$detail->product->name}}</td>
                            <td colspan="1" style="text-align:right;">{{$detail->quantity}}</td>
                            <td colspan="1" style="text-align:right;">{{sprintf('%.2f',round($detail->price,2))}}</td>
                            <td colspan="1" style="text-align:right;">{{sprintf('%.2f',round($detail->subtotal,2))}}</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="1" style="text-align:center;">{{$detail->product->name}}</td>
                            <td colspan="1" style="text-align:right;">{{$detail->quantity}}</td>
                            <td colspan="1" style="text-align:right;">{{sprintf('%.2f',round($detail->price,2))}}</td>
                            <td colspan="1" style="text-align:right;">{{sprintf('%.2f',round($detail->subtotal,2))}}</td>
                        </tr>
                    @endif
                    @endforeach
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:bold;">IGV</td>
                        <td colspan="1" style="font-weight:bold;background:#1b1b1c;color:white;text-align:right;font-size:13px"><b>S/. {{sprintf('%.2f',round($data['purchase']->igv,2))}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:bold;">SUBTOTAL</td>
                        <td colspan="1" style="font-weight:bold;background:#1b1b1c;color:white;text-align:right;font-size:13px"><b>S/. {{sprintf('%.2f',round($data['purchase']->subtotal,2))}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:bold;">TOTAL</td>
                        <td colspan="1" style="font-weight:bold;background:#1b1b1c;color:white;text-align:right;font-size:13px"><b>S/. {{sprintf('%.2f',round($data['purchase']->total,2))}}</b></td>
                    </tr>
                </tbody>
           </table>
            </div>
        </main>

    </body>
</html>
