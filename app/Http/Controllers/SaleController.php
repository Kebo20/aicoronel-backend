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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage as Storage2;

class SaleController extends Controller
{

    public function index()
    {
        return ResourcesSale::collection(Sale::get());
    }
    public function store(Request $request)
    {
        try {
            $valideData = $request->validate([
                'date' => 'required',
                'type_doc' => 'required',
                'number_doc' => 'required',
                'id_client' => 'required',
                'id_storage' => 'required'
            ]);

            if ($request->details == null || $request->details == []) {
                return response()->json([
                    'message' => 'No se ha ingresado ninguna venta.'
                ], 400);
            }

            $exist_sale = Sale::where('number_doc', $request->number_doc)->where('status', 1)->first();
            if ($exist_sale != null) {
                return response()->json([
                    'message' => 'Ya existe una venta con el mismo número de documento'
                ], 400);
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
            $sale->number_doc = strip_tags($request->number_doc);
            $sale->observation = strip_tags($request->observation);
            $sale->id_client = $request->id_client;
            $sale->id_storage = $request->id_storage;
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
        $detail = SaleDetail::where('status', 1)->where('id_sale', $Sale->id_sale)->get();
        $data = array(
            'sale' => $Sale,
            'detail' => $detail
        );
        //$filename = 'Venta' . time() . '.pdf'; //nombre del archivo que el usuario descarga
        $filename = 'Venta' . time() . '.pdf';
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

        $Sale = Sale::where('status', 1)->where('date', $request->date)->get();
        if ($Sale == null) {
            return response()->json([
                'message' => 'No existe una venta con esa fecha',
            ], 400);
        }
        $data = array(
            'sale' => $Sale
        );

        $filename = 'Venta' . date('d-m-Y', strtotime($request->date)) . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.saleadate', compact('data'))->save("storage/sales/" . $filename); //se guarda el archivo

        $url = Storage2::url('sales/' . $filename);
        return response()->json([
            'message' => 'PDF Generado.',
            'data' => URL::to('/') . $url,
            'data2' => $data
        ], 202);
    }
}
