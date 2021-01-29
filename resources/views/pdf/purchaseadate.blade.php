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

            <div style="width: 100%">
                <div style="float: left;position: relative;width:70%">
                    <img src="images/aicoronel.jpg" width="60"  height="80px" alt="">
                </div>
                <div style="background-color:#fcfcfc;border-radius: 16px; border: 0.03cm solid black;float: right;position: relative;width: 35%;text-align: center">
                    <b><span style="font-size: 15px">AGRO IMPORT CORONEL</span></b><br>
                    <span style="font-size: 12px"> RUC: PREGUNTAR</span>
                    <div style="background-color:black;width:100%;padding:0.05cm">
                        <span style="font-size: 12px;color:white">BAGUA / BAGUA GRANDE</span>
                    </div>
                    <span style="font-size: 12px">CELULAR 988730981</span>
                </div>
            </div>

    <br><br><br><br><br><br><br><br><br>
            <div>
            <table class="table_assistance">
                <tr>
                    <td colspan="7" rowspan="1" style="text-align:center; background:#1b1b1c;color:white">
                        <h2><center>REPORTE DE COMPRAS POR DÍA SELECCIONADA</center></h2>
                    </td>
                </tr>
                <thead>
                    <tr>
                        <th style="background:#1b1b1c;color:white">FECHA</th>
                        <th style="background:#1b1b1c;color:white">DOCUMENTO</th>
                        <th style="background:#1b1b1c;color:white">NÚMERO</th>
                        <th style="background:#1b1b1c;color:white">OBSERVACIÓN</th>
                        <th style="background:#1b1b1c; text-align:center;color:white" >PROVEEDOR</th>
                        <th style="background:#1b1b1c; text-align:center;color:white" >SUCURSAL</th>
                        <th style="background:#1b1b1c; text-align:center;color:white" >TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    {{$x = 0}}
                    @foreach($data['purchase'] as $key => $detail)
                    @if($key%2==0)
                        <tr style="background: #dbdedf">
                            <td colspan="1">{{date('d-m-Y', strtotime($detail->date))}}</td>
                            <td colspan="1">{{$detail->type_doc}}</td>
                            <td colspan="1">{{$detail->number_doc}}</td>
                            <td colspan="1">{{$detail->observation}}</td>
                            <td colspan="1" style="text-align:center;">{{$detail->provider->name}}</td>
                            <td colspan="1" style="text-align:center;">{{$detail->storage->name}}</td>
                            <td colspan="1" style="text-align:center;">{{sprintf('%.2f',round($detail->total,2))}}</td>
                        </tr>
                        {{$x = $x + $detail->total}}
                        @else
                        <tr>
                            <td colspan="1">{{date('d-m-Y', strtotime($detail->date))}}</td>
                            <td colspan="1">{{$detail->type_doc}}</td>
                            <td colspan="1">{{$detail->number_doc}}</td>
                            <td colspan="1">{{$detail->observation}}</td>
                            <td colspan="1" style="text-align:center;">{{$detail->provider->name}}</td>
                            <td colspan="1" style="text-align:center;">{{$detail->storage->name}}</td>
                            <td colspan="1" style="text-align:center;">{{sprintf('%.2f',round($detail->total,2))}}</td>
                        </tr>
                        {{$x = $x + $detail->total}}
                    @endif
                    @endforeach
                    <tr>
                        <td colspan="6" style="text-align:right;font-weight:bold;">TOTAL DE COMPRAS POR DÍA</td>
                        <td colspan="1" style="font-weight:bold;background:#1b1b1c;color:white;text-align:center;font-size:13px"><b>S/. {{sprintf('%.2f',round($x,2))}}</b></td>
                    </tr>
                </tbody>
           </table>
            </div>
        </main>

    </body>
</html>
