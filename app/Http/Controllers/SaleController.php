<?php

namespace App\Http\Controllers;

use App\Http\Resources\Sale as ResourcesSale;
use App\Lot;
use App\MoveProduct;
use App\Product;
use App\Client;
use App\Sale;
use App\SaleDetail;
use App\Storage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage as Storage2;

class SaleController extends Controller
{

    public function index()
    {
        if (Auth::user()->id_role == 2) {
            return ResourcesSale::collection(Sale::where('id_storage', '1')->orderBy('date','DESC')->get());
        }
        if (Auth::user()->id_role == 3) {
            return ResourcesSale::collection(Sale::where('id_storage', '2')->orderBy('date','DESC')->get());
        }

        if (Auth::user()->id_role == 1) {
            return ResourcesSale::collection(Sale::orderBy('date','DESC')->get());
        }
    }

    public function correlativo(Request $request){
        if($request->date!=''&&$request->date!=null&&$request->date){
            $year = date_create_from_format('Y-m-d', $request->date)->format('Y');

        }else{
            $year=date('Y');
        }
        $count = Sale::whereYear('date', $year)->where('type_doc',$request->type_doc)->count() +1;
        $lenght=strlen($count);
        $count= str_pad( $count, 8-$lenght, "0", STR_PAD_LEFT);
        if($request->type_doc=='FACTURA'){
            $code = 'FA' . $year . '-' . $count;

        }else

        if($request->type_doc=='BOLETA'){
            $code='B' . $year . '-' . $count;
        }else{
            $code= 'A' . $year . '-' . $count;
        }

        return response()->json([
            'code' => $code
        ], 200);
    }

    public function count()
    {
        if (Auth::user()->id_role == 2) {

            return Sale::where('id_storage', '1')->where('status', '1')->count();
        }

        if (Auth::user()->id_role == 3) {

            return Sale::where('id_storage', '2')->where('status', '1')->count();
        }

        if (Auth::user()->id_role == 1) {

            return Sale::where('status', '1')->count();
        }
    }
    public function store(Request $request)
    {
        try {
            $valideData = $request->validate([
                'date' => 'required',
                'type_doc' => 'required',
                //'number_doc' => 'required',
                'id_client' => 'required',
                // 'id_storage' => 'required'
            ]);

            if ($request->details == null || $request->details == []) {
                return response()->json([
                    'message' => 'No se ha ingresado ninguna venta.'
                ], 400);
            }

            // $exist_sale = Sale::where('number_doc', $request->number_doc)->first();
            // if ($exist_sale != null) {
            //     return response()->json([
            //         'message' => 'Ya existe una venta con el mismo número'
            //     ], 400);
            // }

            if (Auth::user()->id_role == 2) {
                $id_storage = 1;
            }

            if (Auth::user()->id_role == 3) {
                $id_storage = 2;
            }

            if (Auth::user()->id_role == 1) {
                $id_storage = $request->id_storage;
            }

            DB::beginTransaction();


            $client = Client::where('id_client', $request->id_client)->where('status', 1)->first();
            if ($client == null) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Cliente no existe'
                ], 400);
            }

            $storage = Storage::where('id_storage', $request->id_storage)->where('status', 1)->first();
            if ($storage == null) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Almacén no existe'
                ], 400);
            }

            $sale = new Sale();
            $sale->date = strip_tags($request->date);

            $sale->type_doc = strip_tags($request->type_doc);


            $year = date_create_from_format('Y-m-d', $request->date)->format('Y');
            
            $count = Sale::whereYear('date', $year)->where('type_doc',$request->type_doc)->count() +1;
            $lenght=strlen($count);
            $count= str_pad( $count, 8-$lenght, "0", STR_PAD_LEFT);
            if($request->type_doc=='FACTURA'){
                $code = 'FA' . $year . '-' . $count;

            }else

            if($request->type_doc=='BOLETA'){
                $code='B' . $year . '-' . $count;
            }else{
                $code= 'A' . $year . '-' . $count;
            }


            $sale->number_doc = $code;
            $sale->observation = strip_tags($request->observation);
            $sale->id_client = $request->id_client;
            $sale->id_storage = $id_storage;
            $sale->created_by = auth()->id();
            $sale->updated_by = auth()->id();
            $sale->save();

            $total = 0;
            $descTotal = 0;

            foreach ($request->details as $detail) {
                $product = Product::where('id_product', $detail['id_product'])->where('status', 1)->first();
                if ($product == null) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Producto no existe'
                    ], 400);
                }

                //PRECIOS
                if ($product->price!=$detail['price']&&$product->price2!=$detail['price']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Precio ingresado no permitido para: '.$product->name
                    ], 400);
                }


                //CALCULO TOTAL


                $desc = strip_tags($detail['discount']);
                //  $sub = strip_tags($detail['price']) * strip_tags($detail['quantity']) - (($desc)/100 * (strip_tags($detail['price']) * strip_tags($detail['quantity'])));
                $sub = strip_tags($detail['price']) * strip_tags($detail['quantity']) - $desc;

                $total = $total + $sub;
                $descTotal = $descTotal + $desc;

                //

                $lot_old = Lot::where('id_product', $detail['id_product'])->where('id_storage', $sale->id_storage)->first();
                if ($lot_old == null) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'No se encontro producto en almacén: ' . $product->name
                    ], 400);
                }

                if ($detail['quantity'] < 1) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Ingrese cantidades validas para: ' . $product->name
                    ], 500);
                }

                if ($lot_old->quantity < $detail['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Stock insuficiente para: ' . $product->name
                    ], 400);
                }

                $lot_old->quantity -= $detail['quantity'];
                $lot_old->updated_by = auth()->id();
                $lot_old->save();


                $sale_detail = new SaleDetail();
                $sale_detail->quantity = $detail['quantity'];
                $sale_detail->price = $detail['price'];
                $sale_detail->discount =  $desc;
                $sale_detail->subtotal =  $sub;
                $sale_detail->id_sale = $sale->id_sale;
                $sale_detail->id_product = $product->id_product;
                $sale_detail->id_lot = $lot_old->id_lot;
                $sale_detail->created_by = auth()->id();
                $sale_detail->updated_by = auth()->id();
                $sale_detail->save();

                $move_product = new MoveProduct();
                $move_product->date = $sale->date;
                $move_product->type = "salida";
                $move_product->stock = $lot_old->quantity;
                $move_product->quantity = $detail['quantity'];
                $move_product->price = $detail['price'];
                $move_product->total_cost = $detail['price'] * $detail['quantity'];
                $move_product->table_reference = "sales";
                $move_product->id_product = $product->id_product;
                $move_product->id_lot = $lot_old->id_lot;
                $move_product->id_reference = $sale->id_sale;
                $move_product->created_by = auth()->id();
                $move_product->updated_by = auth()->id();
                $move_product->save();
            }
            $subtotal = $total / (1.18);
            $IGVamount = $total - $subtotal;
            $sale->subtotal = $subtotal;
            $sale->igv = $IGVamount;
            $sale->total = $total;
            $sale->discount = $descTotal;
            $sale->save();
            DB::commit();
            return response()->json([
                'message' => 'Venta registrada.',
                'id_sale' => $sale->id_sale
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage()
            ],  500);
        }
    }
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $sale = Sale::where('id_sale', $id)->where('status', 1)->first();
            if ($sale == null) {
                return response()->json([
                    'message' => 'id inválido.'
                ], 400);
            }

            if (Auth::user()->id_role == 2) {
                if ($sale->id_storage != '1') {
                    return response()->json([
                        'message' => 'No puede anular la venta de esta tienda'
                    ], 400);
                }
            }

            if (Auth::user()->id_role == 3) {
                if ($sale->id_storage != '2') {
                    return response()->json([
                        'message' => 'No puede anular la venta de esta tienda'
                    ], 400);
                }
            }

            $sale->status = 0;
            $sale->updated_by = auth()->id();
            $sale->save();

            $sale_detail = SaleDetail::where('id_sale', $sale->id_sale)->where('status', 1)->get();
            foreach ($sale_detail as $detail) {
                $s_d = SaleDetail::where('id_sale_detail', $detail['id_sale_detail'])->where('status', 1)->firstOrFail();
                if ($s_d == null) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Detalle de venta no existe'
                    ], 400);
                }
                $s_d->status = 0;
                $s_d->updated_by = auth()->id();
                $s_d->save();

                $lot_old = Lot::findOrfail($s_d->id_lot);
                $lot_old->quantity += $s_d->quantity;
                $lot_old->updated_by = auth()->id();
                $lot_old->save();

                $move_product = new MoveProduct();
                $move_product->date = $sale->date;
                $move_product->type = "entrada";
                $move_product->stock = $lot_old->quantity;
                $move_product->quantity = $s_d->quantity;
                $move_product->price = $s_d->price;
                $move_product->total_cost = $s_d->price * $s_d->quanty;
                $move_product->table_reference = "sales";
                $move_product->id_product = $s_d->id_product;
                $move_product->id_lot = $lot_old->id_lot;
                $move_product->id_reference = $sale->id_sale;
                $move_product->created_by = auth()->id();
                $move_product->updated_by = auth()->id();
                $move_product->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Venta anulada.'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage()
            ],  500);
        }
    }

    public function show($id)
    {
        $sale = Sale::findOrFail($id);
        if ($sale == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesSale($sale);
    }

    public function print($id)
    {
        if (!$id) {
            return response()->json([
                'message' => 'ID inválido.'
            ], 400);
        }

        $Sale = Sale::findOrFail($id); //busca o falla
        $detail = SaleDetail::where('id_sale', $Sale->id_sale)->get();
        $data = array(
            'sale' => $Sale,
            'detail' => $detail
        );
        $filename = 'Venta_' . $Sale->number_doc . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.sale', compact('data'))->save("storage/sales/" . $filename); //se guarda el archivo

        $url = Storage2::url('sales/' . $filename);
        return response()->json([
            'message' => 'PDF Generado.',
            'data' => URL::to('/') . $url,
            'data2' => $data
        ], 202);
    }

    public function printADate(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required'
        ]);
        if (Auth::user()->id_role == 2) {
            $id_storage = 1;
        }

        if (Auth::user()->id_role == 3) {
            $id_storage = 2;
        }

        if (Auth::user()->id_role == 1) {
            $id_storage = $request->id_storage;
        }

        $Sale = Sale::where('date', $request->date)->where('id_storage', $id_storage)->where('status',1)->get();
        $storage = Storage::findOrFail($id_storage);
        if ($Sale == null || $Sale->count() == 0) {
            return response()->json([
                'message' => 'No existe una venta con esta fecha',
            ], 400);
        }
        $data = array(
            'sale' => $Sale,
            'fecha' => $request->date,
            'storage' => $storage
        );

        $filename = 'Venta_' . date('d-m-Y', strtotime($request->date)) . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.saleadate', compact('data'))->save("storage/sales/" . $filename); //se guarda el archivo

        $url = Storage2::url('sales/' . $filename);
        return response()->json([
            'message' => 'PDF Generado.',
            'data' => URL::to('/') . $url,
            'data2' => $data
        ], 202);
    }

    public function printDateToDate(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required'
        ]);

        if (Auth::user()->id_role == 2) {
            $id_storage = 1;
        }

        if (Auth::user()->id_role == 3) {
            $id_storage = 2;
        }

        if (Auth::user()->id_role == 1) {
            $id_storage = $request->id_storage;
        }

        $Sale = Sale::whereBetween('date', $request->date)->where('id_storage', $id_storage)->where('status',1)->orderBy('date')->get();
        $storage = Storage::findOrFail($id_storage);
        if ($Sale == null || $Sale->count() == 0) {
            return response()->json([
                'message' => 'No existe ventas con ese rango de fechas'
            ], 400);
        }
        $data = array(
            'sale' => $Sale,
            'fecha_inicio' => $request->date[0],
            'fecha_fin' => $request->date[1],
            'storage' => $storage
        );

        $filename = 'Venta_Rango_' . date('d-m-Y', strtotime($request->date[0])) . '_a_' . date('d-m-Y', strtotime($request->date[1])) . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.saledatetodate', compact('data'))->save("storage/sales/" . $filename); //se guarda el archivo

        $url = Storage2::url('sales/' . $filename);
        return response()->json([
            'message' => 'PDF Generado.',
            'data' => URL::to('/') . $url,
            'data2' => $data
        ], 202);
    }

    public function printForMonth(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required'
        ]);

        if (Auth::user()->id_role == 2) {
            $id_storage = 1;
        }

        if (Auth::user()->id_role == 3) {
            $id_storage = 2;
        }

        if (Auth::user()->id_role == 1) {
            $id_storage = $request->id_storage;
        }

        $year = date("Y");
        $Sale = Sale::whereMonth('date', $request->date)->whereYear('date', $year)->where('id_storage', $id_storage)->where('status',1)->orderBy('date')->get();
        $storage = Storage::findOrFail($id_storage);
        if ($Sale == null || $Sale->count() == 0) {
            return response()->json([
                'message' => 'No existe ventas en ese mes'
            ], 400);
        }

        switch ($request->date) {
            case '01':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'ENERO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '02':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'FEBRERO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '03':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'MARZO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '04':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'ABRIL', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '05':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'MAYO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '06':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'JUNIO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '07':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'JULIO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '08':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'AGOSTO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '09':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'SEPTIEMBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '10':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'OCTUBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '11':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'NOVIEMBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '12':
                $data = array(
                    'purchase' => $Sale, 'mes' => $request->date = 'DICIEMBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
        }

        $filename = 'Venta_Mes_' . $request->date . '-' . $year . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.saleformonth', compact('data'))->save("storage/sales/" . $filename); //se guarda el archivo

        $url = Storage2::url('sales/' . $filename);
        return response()->json([
            'message' => 'PDF Generado.',
            'data' => URL::to('/') . $url,
            'data2' => $data
        ], 202);
    }

    public function export($id)
    {
        try {
            $Sale = Sale::findOrFail($id); //busca o falla
            if($Sale->status == 1) {
                $path_real = 'excel/SaleExport.xlsx'; //lee como plantilla
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);
            } else {
                $path_real = 'excel/SaleAnulada.xlsx'; //lee como plantilla
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);
            }
            $worksheet = $spreadsheet->getActiveSheet();

            if (!$id) {
                return response()->json([
                    'message' => 'ID inválido.'
                ], 400);
            }

            $Sale = Sale::findOrFail($id); //busca o falla
            $detail = SaleDetail::where('id_sale', $Sale->id_sale)->get();

            $worksheet->getCell('G8')->setValue(date('d/m/Y', strtotime($Sale->date)));
            $worksheet->getCell('G9')->setValue($Sale->type_doc);
            $worksheet->getCell('G10')->setValue($Sale->number_doc);
            $worksheet->getCell('G11')->setValue($Sale->observation);
            $worksheet->getCell('G12')->setValue($Sale->client->name);
            $worksheet->getCell('G13')->setValue($Sale->storage->name);

            $cell = 16;
            foreach ($detail as $sd) {
                $worksheet->getCell('B' . $cell)->setValue($sd->product->name);
                $worksheet->getCell('D' . $cell)->setValue($sd->quantity);
                $worksheet->getCell('E' . $cell)->setValue($sd->price);
                $worksheet->getCell('F' . $cell)->setValue($sd->discount);
                $worksheet->getCell('G' . $cell)->setValue($sd->subtotal);

                $cell = $cell + 1;
            }

            $igv = $cell;
            $subtotal = $cell + 1;
            $total = $cell + 2;
            $worksheet->getCell('F' . $igv)->setValue('IGV');
            $worksheet->getCell('F' . $subtotal)->setValue('SUBTOTAL');
            $worksheet->getCell('F' . $total)->setValue('TOTAL');
            $worksheet->getCell('G' . $igv)->setValue($Sale->igv);
            $worksheet->getCell('G' . $subtotal)->setValue($Sale->subtotal);
            $worksheet->getCell('G' . $total)->setValue($Sale->total);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/sales/Venta_" . $Sale->number_doc . '.xlsx'); //la salida

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/sales/Venta_" . $Sale->number_doc . '.xlsx'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage()
            ],  500);
        }
    }

    public function exportADate(Request $request)
    {
        try {
            $path_real = 'excel/SaleADate.xlsx';
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);

            $worksheet = $spreadsheet->getActiveSheet();

            $validatedData = $request->validate([
                'date' => 'required'
            ]);
            if (Auth::user()->id_role == 2) {
                $id_storage = 1;
            }

            if (Auth::user()->id_role == 3) {
                $id_storage = 2;
            }

            if (Auth::user()->id_role == 1) {
                $id_storage = $request->id_storage;
            }


            $Sale = Sale::where('date', $request->date)->where('id_storage', $id_storage)->where('status',1)->get();
            $storage = Storage::findOrFail($id_storage);
            if ($Sale == null || $Sale->count() == 0) {
                return response()->json([
                    'message' => 'No existe una venta con esa fecha'
                ], 400);
            }

            $worksheet->getCell('B7')->setValue('VENTAS DEL DÍA ' . date('d/m/Y', strtotime($request->date)) . ' (' . $storage->name . ')');

            $x = 0;
            $cell = 9;
            foreach ($Sale as $sale) {
                $worksheet->getCell('B' . $cell)->setValue(date('d/m/Y', strtotime($sale->date)));
                $worksheet->getCell('C' . $cell)->setValue($sale->type_doc);
                $worksheet->getCell('D' . $cell)->setValue($sale->number_doc);
                $worksheet->getCell('E' . $cell)->setValue($sale->observation);
                $worksheet->getCell('F' . $cell)->setValue($sale->client->name);
                $worksheet->getCell('G' . $cell)->setValue($sale->discount);
                $worksheet->getCell('H' . $cell)->setValue($sale->igv);
                $worksheet->getCell('I' . $cell)->setValue($sale->subtotal);
                $worksheet->getCell('J' . $cell)->setValue($sale->total);

                $cell = $cell + 1;
                $x = $x + $sale->total;
            }

            $worksheet->getCell('I' . $cell)->setValue('TOTAL');
            $worksheet->getCell('J' . $cell)->setValue(sprintf('%.2f', round($x, 2)));

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/sales/Venta_" . date('d-m-Y', strtotime($request->date)) . '.xlsx'); //la salida

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/sales/Venta_" . date('d-m-Y', strtotime($request->date)) . '.xlsx'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage()
            ],  500);
        }
    }

    public function exportDateToDate(Request $request)
    {
        try {
            $path_real = 'excel/SaleDateToDate.xlsx';
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);

            $worksheet = $spreadsheet->getActiveSheet();

            $validatedData = $request->validate([
                'date' => 'required'
            ]);

            if (Auth::user()->id_role == 2) {
                $id_storage = 1;
            }

            if (Auth::user()->id_role == 3) {
                $id_storage = 2;
            }

            if (Auth::user()->id_role == 1) {
                $id_storage = $request->id_storage;
            }


            $Sale = Sale::whereBetween('date', $request->date)->where('id_storage', $id_storage)->where('status',1)->orderBy('date')->get();
            $storage = Storage::findOrFail($id_storage);
            if ($Sale == null || $Sale->count() == 0) {
                return response()->json([
                    'message' => 'No existe ventas con ese rango de fechas'
                ], 400);
            }

            $worksheet->getCell('B7')->setValue('VENTAS ENTRE FECHAS ' . date('d/m/Y', strtotime($request->date[0])) . ' - ' . date('d/m/Y', strtotime($request->date[1])) . ' (' . $storage->name . ')');

            $x = 0;
            $cell = 9;
            foreach ($Sale as $sale) {
                $worksheet->getCell('B' . $cell)->setValue(date('d/m/Y', strtotime($sale->date)));
                $worksheet->getCell('C' . $cell)->setValue($sale->type_doc);
                $worksheet->getCell('D' . $cell)->setValue($sale->number_doc);
                $worksheet->getCell('E' . $cell)->setValue($sale->observation);
                $worksheet->getCell('F' . $cell)->setValue($sale->client->name);
                $worksheet->getCell('G' . $cell)->setValue($sale->discount);
                $worksheet->getCell('H' . $cell)->setValue($sale->igv);
                $worksheet->getCell('I' . $cell)->setValue($sale->subtotal);
                $worksheet->getCell('J' . $cell)->setValue($sale->total);

                $cell = $cell + 1;
                $x = $x + $sale->total;
            }

            $worksheet->getCell('I' . $cell)->setValue('TOTAL');
            $worksheet->getCell('J' . $cell)->setValue(sprintf('%.2f', round($x, 2)));

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/sales/Venta_Rango_" . date('d-m-Y', strtotime($request->date[0])) . '_a_' . date('d-m-Y', strtotime($request->date[1])) . '.xlsx');

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/sales/Venta_Rango_" . date('d-m-Y', strtotime($request->date[0])) . '_a_' . date('d-m-Y', strtotime($request->date[1])) . '.xlsx'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage()
            ],  500);
        }
    }

    public function exportForMonth(Request $request)
    {
        try {
            $path_real = 'excel/SaleForMonth.xlsx';
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);

            $worksheet = $spreadsheet->getActiveSheet();

            $validatedData = $request->validate([
                'date' => 'required'
            ]);
            if (Auth::user()->id_role == 2) {
                $id_storage = 1;
            }

            if (Auth::user()->id_role == 3) {
                $id_storage = 2;
            }

            if (Auth::user()->id_role == 1) {
                $id_storage = $request->id_storage;
            }

            $year = date("Y");
            $Sale = Sale::whereMonth('date', $request->date)->whereYear('date', $year)->where('status',1)->where('id_storage', $id_storage)->orderBy('date')->get();
            $storage = Storage::findOrFail($id_storage);
            if ($Sale == null || $Sale->count() == 0) {
                return response()->json([
                    'message' => 'No existe ventas en ese mes'
                ], 400);
            }

            switch ($request->date) {
                case '01':
                    $request->date = 'ENERO';
                    break;
                case '02':
                    $request->date = 'FEBRERO';
                    break;
                case '03':
                    $request->date = 'MARZO';
                    break;
                case '04':
                    $request->date = 'ABRIL';
                    break;
                case '05':
                    $request->date = 'MAYO';
                    break;
                case '06':
                    $request->date = 'JUNIO';
                    break;
                case '07':
                    $request->date = 'JULIO';
                case '08':
                    $request->date = 'AGOSTO';
                    break;
                case '09':
                    $request->date = 'SEPTIEMBRE';
                    break;
                case '10':
                    $request->date = 'OCTUBRE';
                    break;
                case '11':
                    $request->date = 'NOVIEMBRE';
                    break;
                case '12':
                    $request->date = 'DICIEMBRE';
                    break;
            }

            $worksheet->getCell('B7')->setValue('VENTAS DEL MES DE ' . $request->date . ' DEl ' . $year . ' (' . $storage->name . ')');

            $x = 0;
            $cell = 9;
            foreach ($Sale as $sale) {
                $worksheet->getCell('B' . $cell)->setValue(date('d/m/Y', strtotime($sale->date)));
                $worksheet->getCell('C' . $cell)->setValue($sale->type_doc);
                $worksheet->getCell('D' . $cell)->setValue($sale->number_doc);
                $worksheet->getCell('E' . $cell)->setValue($sale->observation);
                $worksheet->getCell('F' . $cell)->setValue($sale->client->name);
                $worksheet->getCell('G' . $cell)->setValue($sale->discount);
                $worksheet->getCell('H' . $cell)->setValue($sale->igv);
                $worksheet->getCell('I' . $cell)->setValue($sale->subtotal);
                $worksheet->getCell('J' . $cell)->setValue($sale->total);

                $cell = $cell + 1;
                $x = $x + $sale->total;
            }

            $worksheet->getCell('I' . $cell)->setValue('TOTAL');
            $worksheet->getCell('J' . $cell)->setValue(sprintf('%.2f', round($x, 2)));

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/sales/Venta_Mes_" . $request->date . '-' . $year . '.xlsx');

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/sales/Venta_Mes_" . $request->date . '-' . $year . '.xlsx'
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage()
            ],  500);
        }
    }

    public function totalForMonth()
    {

        // $year = date("Y");

        // if (Auth::user()->id_role == 2) {
        //     // return ResourcesPurchase::collection(Purchase::where('id_storage', '1')->where('status', '1')->orderBy('date')->get());
        //    return DB::select("select DATE_FORMAT(DATE, '%M', 'es_ES')  as month,SUM(total) as total from sales where id_storage='1' YEAR(DATE)=".$year." and status=1  GROUP BY month ORDER BY date ASC");

        // }

        // if (Auth::user()->id_role == 3) {
        //     // return ResourcesPurchase::collection(Purchase::where('id_storage', '2')->where('status', '1')->orderBy('date')->get());
        //    return DB::select("select DATE_FORMAT(DATE, '%M', 'es_ES')  as month,SUM(total) as total from sales where id_storage='2' YEAR(DATE)=".$year." and status=1  GROUP BY month ORDER BY date ASC");

        // }

        // if (Auth::user()->id_role == 1) {
        //    // return ResourcesPurchase::collection(Purchase::where('status', '1')->orderBy('date')->get());
        //    return DB::select("select DATE_FORMAT(DATE, '%M', 'es_ES')  as month,SUM(total) as total from sales where  YEAR(DATE)=".$year." and status=1  GROUP BY month ORDER BY date ASC");

        // }

        $year = date("Y");

        if (Auth::user()->id_role == 2) {
            // return ResourcesPurchase::collection(Purchase::where('id_storage', '1')->where('status', '1')->orderBy('date')->get());
            DB::select("SET lc_time_names = 'es_ES';");
           return DB::select("select DATE_FORMAT(date, '%M %Y')  as month,SUM(total) as total from sales where id_storage='1' and YEAR(date)=".$year." and status=1 GROUP BY MONTH(date),month  ORDER BY MONTH(date) ASC LIMIT 12");

        }

        if (Auth::user()->id_role == 3) {
            DB::select("SET lc_time_names = 'es_ES';");
            // return ResourcesPurchase::collection(Purchase::where('id_storage', '2')->where('status', '1')->orderBy('date')->get());
           return DB::select("select DATE_FORMAT(date, '%M %Y')  as month,SUM(total) as total from sales where id_storage='2' and YEAR(date)=".$year." and status=1 GROUP BY MONTH(date),month  ORDER BY MONTH(date) ASC LIMIT 12");

        }

        if (Auth::user()->id_role == 1) {
            DB::select("SET lc_time_names = 'es_ES';");
           // return ResourcesPurchase::collection(Purchase::where('status', '1')->orderBy('date')->get());
           return DB::select("select DATE_FORMAT(date, '%M %Y')  as month,SUM(total) as total from sales where  YEAR(date)=".$year." and status=1 GROUP BY MONTH(date),month  ORDER BY MONTH(date) ASC LIMIT 12");

        }
    }
}
