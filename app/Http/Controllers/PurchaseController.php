<?php

namespace App\Http\Controllers;

use App\Http\Resources\Purchase as ResourcesPurchase;
use App\Lot;
use App\MoveProduct;
use App\Product;
use App\Provider;
use App\Purchase;
use App\PurchaseDetail;
use App\Storage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage as Storage2;

class PurchaseController extends Controller
{
    public function index() {
        return ResourcesPurchase::collection(Purchase::get());
    }
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|max:255',
                'type_doc' => 'required',
                'number_doc' => 'required|numeric',
                'id_provider' => 'required',
                'id_storage' => 'required'
            ]);

            if ($request->details == null || $request->details == []) {
                return response()->json([
                    'message' => 'No se ha ingresado ningún compra.'
                ], 400);
            }

            $exist_purchase = Purchase::where('number_doc', $request->number_doc)->where('status', 1)->first();
            if ($exist_purchase != null) {
                return response()->json([
                    'message' => 'Ya existe una compra con el mismo número de documento.'
                ], 400);
            }

            DB::beginTransaction();

            //Cálculo del total
            $total = 0;
            foreach ($request->details as $detail) {
                //$sub = 0;
                $sub = strip_tags($detail['price']) * strip_tags($detail['quantity']);
                $total = $total + $sub;
            }
            $subtotal = $total / (1.18);
            $IGVamount = $total - $subtotal;

            $provider = Provider::where('id_provider', $request->id_provider)->where('status', 1)->first();
            if ($provider == null) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Proveedor no existe'
                ], 400);
            }

            $storage = Storage::where('id_storage', $request->id_storage)->where('status', 1)->first();
            if ($storage == null) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Almacén no existe'
                ], 400);
            }

            $purchase = new Purchase();
            $purchase->date = strip_tags($request->date);
            $purchase->subtotal = $subtotal;
            $purchase->igv = $IGVamount;
            $purchase->total = $total;
            $purchase->type_doc = strip_tags($request->type_doc);
            $purchase->number_doc = strip_tags($request->number_doc);
            $purchase->observation = strip_tags($request->observation);
            $purchase->id_provider = $request->id_provider;
            $purchase->id_storage = $request->id_storage;
            $purchase->created_by = auth()->id();
            $purchase->updated_by = auth()->id();
            $purchase->save();

            foreach ($request->details as $detail) {
                $product = Product::where('id_product', $detail['id_product'])->where('status', 1)->first();
                if ($product == null) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Producto no existe'
                    ], 400);
                }

                $lot_old = Lot::where('id_product', $detail['id_product'])->where('id_storage', $purchase->id_storage)->first();
                if ($lot_old == null) {
                    $lot_new = new Lot();
                    $lot_new->quantity = $detail['quantity'];
                    $lot_new->id_product = $detail['id_product'];
                    $lot_new->id_storage = $request->id_storage;
                    $lot_new->created_by = auth()->id();
                    $lot_new->updated_by = auth()->id();
                    $lot_new->save();

                    $lot = $lot_new;
                } else {
                    $lot_old->quantity += $detail['quantity'];
                    $lot_old->updated_by = auth()->id();
                    $lot_old->save();

                    $lot = $lot_old;
                }

                $purchase_detail = new PurchaseDetail();
                $purchase_detail->quantity = $detail['quantity'];
                $purchase_detail->price = $detail['price'];
                $purchase_detail->subtotal = $detail['quantity'] * $detail['price'];
                $purchase_detail->id_purchase = $purchase->id_purchase;
                $purchase_detail->id_product = $product->id_product;
                $purchase_detail->id_lot = $lot->id_lot;
                $purchase_detail->created_by = auth()->id();
                $purchase_detail->updated_by = auth()->id();
                $purchase_detail->save();

                $move_product = new MoveProduct();
                $move_product->date = $purchase->date;
                $move_product->type = "entrada";
                $move_product->stock = $lot->quantity;
                $move_product->quantity = $detail['quantity'];
                $move_product->price = $detail['price'];
                $move_product->total_cost = $detail['price'] * $detail['quantity'];
                $move_product->table_reference = "purchases";
                $move_product->id_product = $product->id_product;
                $move_product->id_lot = $lot->id_lot;
                $move_product->id_reference = $purchase->id_purchase;
                $move_product->created_by = auth()->id();
                $move_product->updated_by = auth()->id();
                $move_product->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Compra registrada.',
                'id_purchase' => $purchase->id_purchase
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
            /*
            return response()->json([
                'message' => date("Y-m-d H:i:s")
            ]);
            */
            DB::beginTransaction();
            $purchase = Purchase::where('id_purchase', $id)->where('status', 1)->first();
            if ($purchase == null) {
                return response()->json([
                    'message' => 'id inválido.'
                ], 400);
            }

            $purchase->status = 0;
            $purchase->updated_by = auth()->id();
            $purchase->save();

            $purchase_detail = PurchaseDetail::where('id_purchase', $purchase->id_purchase)->where('status', 1)->get();
            foreach ($purchase_detail as $detail) {
                $p_d = PurchaseDetail::where('id_purchase_detail', $detail['id_purchase_detail'])->where('status', 1)->firstOrFail();
                if ($p_d == null) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Detalle Compra no existe'
                    ], 400);
                }
                $p_d->status = 0;
                $p_d->updated_by = auth()->id();
                $p_d->save();
                $lot_old = Lot::findOrfail($p_d->id_lot);
                $lot_old->quantity -= $p_d->quantity;
                $lot_old->updated_by = auth()->id();
                $lot_old->save();

                $move_product = new MoveProduct();
                $move_product->date = $purchase->date;
                $move_product->type = "salida";
                $move_product->stock = $lot_old->quantity;
                $move_product->quantity = $p_d->quantity;
                $move_product->price = $p_d->price;
                $move_product->total_cost = $p_d->price * $p_d->quanty;
                $move_product->table_reference = "purchases";
                $move_product->id_product = $p_d->id_product;
                $move_product->id_lot = $lot_old->id_lot;
                $move_product->id_reference = $purchase->id_purchase;
                $move_product->created_by = auth()->id();
                $move_product->updated_by = auth()->id();
                $move_product->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Compra anulada.'
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
        $purchase = Purchase::findOrFail($id);
        if ($purchase == null)
            return response()->json([
                'message' => 'id inválido.'
            ], 400);

        return new ResourcesPurchase($purchase);
    }

    public function print($id)
    {
        if (!$id) {
            return response()->json([
                'message' => 'ID inválido.'
            ], 400);
        }

        $Purchase = Purchase::findOrFail($id); //busca o falla
        $detail = PurchaseDetail::where('status', 1)->where('id_purchase', $Purchase->id_purchase)->get();
        $data = array(
            'purchase' => $Purchase,
            'detail' => $detail
        );
        $filename = 'Compra' . time() . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.purchase', compact('data'))->save("storage/purchases/" . $filename); //se guarda el archivo

        $url = Storage2::url('purchases/' . $filename);
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

        $Purchase = Purchase::where('status', 1)->where('date', $request->date)->get();
        if ($Purchase == null) {
            return response()->json([
                'message' => 'No existe una compra con esa fecha',
            ], 400);
        }
        $data = array(
            'purchase' => $Purchase
        );

        $filename = 'Compra' . date('d-m-Y', strtotime($request->date)) . '.pdf';//nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.purchaseadate', compact('data'))->save("storage/purchases/" . $filename); //se guarda el archivo

        $url = Storage2::url('purchases/' . $filename);
        return response()->json([
            'message' => 'PDF Generado.',
            'data' => URL::to('/') . $url,
            'data2' => $data
        ], 202);
    }

    /*
    public function export(Request $request)
    {
        try {
            $path_real = 'excel/formato_exportacion_orden_compra.xlsx'; //lee como plantilla
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);

            $worksheet = $spreadsheet->getActiveSheet();


            $purchase_orders = PurchaseOrder::where('status', 1)->get();

            $index_cell = 2;

            foreach ($purchase_orders as  $purchase_order) {


                $worksheet->getCell('A' . $index_cell)->setValue($purchase_order->code);
                $worksheet->getCell('B' . $index_cell)->setValue($purchase_order->date);
                if ($purchase_order->type == '1') {
                    $worksheet->getCell('C' . $index_cell)->setValue('Compra');
                } else {
                    $worksheet->getCell('C' . $index_cell)->setValue('Servicio');
                }
                $worksheet->getCell('D' . $index_cell)->setValue($purchase_order->provider->businessName);
                $worksheet->getCell('E' . $index_cell)->setValue($purchase_order->order->code);
                $worksheet->getCell('F' . $index_cell)->setValue($purchase_order->description);
                $worksheet->getCell('G' . $index_cell)->setValue($purchase_order->IGVamount);
                $worksheet->getCell('H' . $index_cell)->setValue($purchase_order->subtotal);
                $worksheet->getCell('I' . $index_cell)->setValue($purchase_order->total);

                if ($purchase_order->active == '1') {
                    $worksheet->getCell('J' . $index_cell)->setValue('Activo');
                } else {
                    $worksheet->getCell('J' . $index_cell)->setValue('Inactivo');
                }

                $index_cell++;
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');


            $writer->save("storage/ordenes_compra/Listado_ordenes_compra_" . time() . '.xlsx');//la salida

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/ordenes_compra/Listado_ordenes_compra_" . time() . '.xlsx'
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage(),

            ],  500);
        }
    }
}
    */
}
