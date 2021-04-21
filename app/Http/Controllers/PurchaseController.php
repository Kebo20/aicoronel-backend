<?php

namespace App\Http\Controllers;

use App\Http\Resources\Purchase as ResourcesPurchase;
use App\Http\Resources\PurchaseDetail as ResourcesPurchaseDetail;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage as Storage2;

class PurchaseController extends Controller
{
    public function index()
    {
        if (Auth::user()->id_role == 2) {

            return ResourcesPurchase::collection(Purchase::where('id_storage', '1')->orderBy('date', 'DESC')->get());
        }

        if (Auth::user()->id_role == 3) {

            return ResourcesPurchase::collection(Purchase::where('id_storage', '2')->orderBy('date', 'DESC')->get());
        }

        if (Auth::user()->id_role == 1) {

            return ResourcesPurchase::collection(Purchase::orderBy('date', 'DESC')->get());
        }
    }
    public function searchPurchaseByProduct(Request $request)
    {
        return ResourcesPurchaseDetail::collection(PurchaseDetail::where('id_product', $request->id_product)
            ->where('status', 1)
            ->orderBy('id_purchase_detail', 'DESC')->take(10)->get());
    }
    public function count()
    {

        if (Auth::user()->id_role == 2) {

            return Purchase::where('id_storage', '1')->where('status', '1')->count();
        }

        if (Auth::user()->id_role == 3) {

            return Purchase::where('id_storage', '2')->where('status', '1')->count();
        }

        if (Auth::user()->id_role == 1) {

            return Purchase::where('status', '1')->count();
        }
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|max:255',
                'type_doc' => 'required',
                'number_doc' => 'required',
                'id_provider' => 'required',
                //'id_storage' => 'required'
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
            $purchase->id_storage = $id_storage;
            $purchase->created_by = auth()->id();
            $purchase->created_at = date('Y-m-d H:i:s');
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
                if ($detail['quantity'] <= 0) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Cantidad no válida para ' . $product->name,
                    ], 400);
                }

                if ($detail['price'] <= 0) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Precio no válido para ' . $product->name,
                    ], 400);
                }

                $lot_old = Lot::where('id_product', $detail['id_product'])->where('id_storage', $purchase->id_storage)->first();
                if ($lot_old == null) {
                    $lot_new = new Lot();
                    $lot_new->quantity = strip_tags($detail['quantity']);
                    $lot_new->id_product = strip_tags($detail['id_product']);
                    $lot_new->id_storage = $request->id_storage;
                    $lot_new->created_by = auth()->id();
                    $lot_new->created_at = date('Y-m-d H:i:s');
                    $lot_new->updated_by = auth()->id();
                    $lot_new->save();

                    $lot = $lot_new;
                } else {
                    $lot_old->quantity += strip_tags($detail['quantity']);
                    $lot_old->updated_by = auth()->id();
                    $lot_old->updated_at = date('Y-m-d H:i:s');

                    $lot_old->save();

                    $lot = $lot_old;
                }

                $purchase_detail = new PurchaseDetail();
                $purchase_detail->quantity = strip_tags($detail['quantity']);
                $purchase_detail->price = strip_tags($detail['price']);
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
                $move_product->quantity = strip_tags($detail['quantity']);
                $move_product->price = strip_tags($detail['price']);
                $move_product->total_cost = $detail['price'] * $detail['quantity'];
                $move_product->table_reference = "purchases";
                $move_product->id_product = $product->id_product;
                $move_product->id_lot = $lot->id_lot;
                $move_product->id_reference = $purchase->id_purchase;
                $move_product->created_by = auth()->id();
                $move_product->created_at = date('Y-m-d H:i:s');
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

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|max:255',
                'type_doc' => 'required',
                'number_doc' => 'required',
                'id_provider' => 'required',
                'id_storage' => 'required'
            ]);

            if ($request->details == null || $request->details == []) {
                return response()->json([
                    'message' => 'No se ha ingresado ningún compra.'
                ], 400);
            }

            $purchase = Purchase::where('id_purchase', $id)->where('status', 1)->first();
            if ($purchase == null) {
                return response()->json([
                    'message' => 'No se puede editar esta compra.'
                ], 400);
            }

            // if (Auth::user()->id_role == 2) {
            //     $id_storage = 1;
            // }

            // if (Auth::user()->id_role == 3) {
            //     $id_storage = 2;
            // }

            if (Auth::user()->id_role == 1) {
                $id_storage = $request->id_storage;
            } else {
                return response()->json([
                    'message' => 'No puede editar esta compra.'
                ], 400);
            }

            DB::beginTransaction();

            //Deshacer cambios 
            $a_purchase_detail = PurchaseDetail::where('id_purchase', $purchase->id_purchase)->where('status', 1)->get();
            foreach ($a_purchase_detail as $detail) {
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
                $lot_old->updated_at = date('Y-m-d H:i:s');
                $lot_old->save();

                $move_product = new MoveProduct();
                $move_product->date = strip_tags($request->date);
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
                $move_product->created_at = date('Y-m-d H:i:s');
                $move_product->updated_by = auth()->id();
                $move_product->save();
            }


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

            $purchase->date = strip_tags($request->date);
            $purchase->subtotal = $subtotal;
            $purchase->igv = $IGVamount;
            $purchase->total = $total;
            $purchase->type_doc = strip_tags($request->type_doc);
            $purchase->number_doc = strip_tags($request->number_doc);
            $purchase->observation = strip_tags($request->observation);
            $purchase->id_provider = $request->id_provider;
            $purchase->id_storage = $id_storage;
            $purchase->updated_at = date('Y-m-d H:i:s');
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

                if ($detail['quantity'] <= 0) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Cantidad no válida para ' . $product->name,
                    ], 400);
                }

                if ($detail['price'] <= 0) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Precio no válido para ' . $product->name,
                    ], 400);
                }

                $lot_old = Lot::where('id_product', $detail['id_product'])->where('id_storage', $purchase->id_storage)->first();
                if ($lot_old == null) {
                    $lot_new = new Lot();
                    $lot_new->quantity = strip_tags($detail['quantity']);
                    $lot_new->id_product = strip_tags($detail['id_product']);
                    $lot_new->id_storage = $request->id_storage;
                    $lot_new->created_by = auth()->id();
                    $lot_new->created_at = date('Y-m-d H:i:s');
                    $lot_new->updated_by = auth()->id();
                    $lot_new->save();

                    $lot = $lot_new;
                } else {
                    $lot_old->quantity += strip_tags($detail['quantity']);
                    $lot_old->updated_by = auth()->id();
                    $lot_old->updated_at = date('Y-m-d H:i:s');
                    $lot_old->save();

                    $lot = $lot_old;
                }

                $purchase_detail = new PurchaseDetail();
                $purchase_detail->quantity = strip_tags($detail['quantity']);
                $purchase_detail->price = strip_tags($detail['price']);
                $purchase_detail->subtotal = $detail['quantity'] * $detail['price'];
                $purchase_detail->id_purchase = $purchase->id_purchase;
                $purchase_detail->id_product = $product->id_product;
                $purchase_detail->id_lot = $lot->id_lot;
                $purchase_detail->created_by = auth()->id();
                $purchase_detail->updated_by = auth()->id();
                $purchase_detail->save();

                $move_product = new MoveProduct();
                $move_product->date = strip_tags($request->date);
                $move_product->type = "entrada";
                $move_product->stock = $lot->quantity;
                $move_product->quantity = strip_tags($detail['quantity']);
                $move_product->price = strip_tags($detail['price']);
                $move_product->total_cost = $detail['price'] * $detail['quantity'];
                $move_product->table_reference = "purchases";
                $move_product->id_product = $product->id_product;
                $move_product->id_lot = $lot->id_lot;
                $move_product->id_reference = $purchase->id_purchase;
                $move_product->created_by = auth()->id();
                $move_product->created_at = date('Y-m-d H:i:s');
                $move_product->updated_by = auth()->id();
                $move_product->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Compra actualizada correctamente.',
            ], 200);
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
            $purchase = Purchase::where('id_purchase', $id)->where('status', 1)->first();
            if ($purchase == null) {
                return response()->json([
                    'message' => 'id inválido.'
                ], 400);
            }



            if (Auth::user()->id_role == 2) {
                if ($purchase->id_storage != '1') {
                    return response()->json([
                        'message' => 'No puede anular la compra de esta tienda'
                    ], 400);
                }
            }

            if (Auth::user()->id_role == 3) {
                if ($purchase->id_storage != '2') {
                    return response()->json([
                        'message' => 'No puede anular la compra de esta tienda'
                    ], 400);
                }
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
        $detail = PurchaseDetail::where('id_purchase', $Purchase->id_purchase)->where('status',1)->get();
        $data = array(
            'purchase' => $Purchase,
            'detail' => $detail
        );
        $filename = 'Compra_' . $Purchase->number_doc . '.pdf'; //nombre del archivo que el usuario descarga
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

        if (Auth::user()->id_role == 2) {
            $id_storage = 1;
        }

        if (Auth::user()->id_role == 3) {
            $id_storage = 2;
        }

        if (Auth::user()->id_role == 1) {
            $id_storage = $request->id_storage;
        }

        $Purchase = Purchase::where('status', 1)->where('date', $request->date)->where('id_storage', $id_storage)->get();
        $storage = Storage::findOrFail($id_storage);
        if ($Purchase == null || $Purchase->count() == 0) {
            return response()->json([
                'message' => 'No existe una compra con esa fecha'
            ], 400);
        }
        $data = array(
            'purchase' => $Purchase,
            'fecha' => $request->date,
            'storage' => $storage
        );

        $filename = 'Compra_' . date('d-m-Y', strtotime($request->date)) . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.purchaseadate', compact('data'))->save("storage/purchases/" . $filename); //se guarda el archivo

        $url = Storage2::url('purchases/' . $filename);
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

        $Purchase = Purchase::where('status', 1)->whereBetween('date', $request->date)->where('id_storage', $id_storage)
            ->orderBy('date')->get();
        $storage = Storage::findOrFail($id_storage);
        if ($Purchase == null || $Purchase->count() == 0) {
            return response()->json([
                'message' => 'No existe compras con ese rango de fechas'
            ], 400);
        }
        $data = array(
            'purchase' => $Purchase,
            'fecha_inicio' => $request->date[0],
            'fecha_fin' => $request->date[1],
            'storage' => $storage
        );

        $filename = 'Compra_Rango_' . date('d-m-Y', strtotime($request->date[0])) . '_a_' . date('d-m-Y', strtotime($request->date[1])) . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.purchasedatetodate', compact('data'))->save("storage/purchases/" . $filename); //se guarda el archivo

        $url = Storage2::url('purchases/' . $filename);
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
        $Purchase = Purchase::where('status', 1)->whereMonth('date', $request->date)->whereYear('date', $year)->where('id_storage', $id_storage)
            ->orderBy('date')->get();
        $storage = Storage::findOrFail($id_storage);
        if ($Purchase == null || $Purchase->count() == 0) {
            return response()->json([
                'message' => 'No existe compras en ese mes'
            ], 400);
        }

        switch ($request->date) {
            case '01':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'ENERO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '02':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'FEBRERO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '03':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'MARZO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '04':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'ABRIL', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '05':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'MAYO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '06':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'JUNIO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '07':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'JULIO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '08':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'AGOSTO', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '09':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'SEPTIEMBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '10':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'OCTUBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '11':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'NOVIEMBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
            case '12':
                $data = array(
                    'purchase' => $Purchase, 'mes' => $request->date = 'DICIEMBRE', 'ano' => $year, 'storage' => $storage
                );
                break;
        }

        $filename = 'Compra_Mes_' . $request->date . '-' . $year . '.pdf'; //nombre del archivo que el usuario descarga
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/pdf.log'), 'tempDir' => storage_path('logs/')])->loadView('pdf.purchaseformonth', compact('data'))->save("storage/purchases/" . $filename); //se guarda el archivo

        $url = Storage2::url('purchases/' . $filename);
        return response()->json([
            'message' => 'PDF Generado.',
            'data' => URL::to('/') . $url,
            'data2' => $data
        ], 202);
    }

    public function export($id)
    {
        try {
            $Purchase = Purchase::findOrFail($id); //busca o falla
            if ($Purchase->status == 1) {
                $path_real = 'excel/PurchaseExport.xlsx'; //lee como plantilla
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);
            } else {
                $path_real = 'excel/PurchaseAnulada.xlsx'; //lee como plantilla
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path_real);
            }

            $worksheet = $spreadsheet->getActiveSheet();

            if (!$id) {
                return response()->json([
                    'message' => 'ID inválido.'
                ], 400);
            }

            $detail = PurchaseDetail::where('id_purchase', $Purchase->id_purchase)->where('status',1)->get();

            $worksheet->getCell('F8')->setValue(date('d/m/Y', strtotime($Purchase->date)));
            $worksheet->getCell('F9')->setValue($Purchase->type_doc);
            $worksheet->getCell('F10')->setValue($Purchase->number_doc);
            $worksheet->getCell('F11')->setValue($Purchase->observation);
            $worksheet->getCell('F12')->setValue($Purchase->provider->name);
            $worksheet->getCell('F13')->setValue($Purchase->storage->name);

            $cell = 16;
            foreach ($detail as $pd) {
                $worksheet->getCell('B' . $cell)->setValue($pd->product->name);
                $worksheet->getCell('D' . $cell)->setValue($pd->quantity);
                $worksheet->getCell('E' . $cell)->setValue($pd->price);
                $worksheet->getCell('F' . $cell)->setValue($pd->subtotal);

                $cell = $cell + 1;
            }

            $igv = $cell;
            $subtotal = $cell + 1;
            $total = $cell + 2;
            $worksheet->getCell('E' . $igv)->setValue('IGV');
            $worksheet->getCell('E' . $subtotal)->setValue('SUBTOTAL');
            $worksheet->getCell('E' . $total)->setValue('TOTAL');
            $worksheet->getCell('F' . $igv)->setValue($Purchase->igv);
            $worksheet->getCell('F' . $subtotal)->setValue($Purchase->subtotal);
            $worksheet->getCell('F' . $total)->setValue($Purchase->total);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/purchases/Compra_" . $Purchase->number_doc . '.xlsx'); //la salida

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/purchases/Compra_" . $Purchase->number_doc . '.xlsx'
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
            $path_real = 'excel/PurchaseADate.xlsx';
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

            $Purchase = Purchase::where('status', 1)->where('date', $request->date)->where('id_storage', $id_storage)->get();
            $storage = Storage::findOrFail($id_storage);
            if ($Purchase == null || $Purchase->count() == 0) {
                return response()->json([
                    'message' => 'No existe una compra con esa fecha'
                ], 400);
            }

            $worksheet->getCell('B7')->setValue('COMPRAS DEL DÍA ' . date('d/m/Y', strtotime($request->date)) . ' (' . $storage->name . ')');

            $x = 0;
            $cell = 9;
            foreach ($Purchase as $purchase) {
                $worksheet->getCell('B' . $cell)->setValue(date('d/m/Y', strtotime($purchase->date)));
                $worksheet->getCell('C' . $cell)->setValue($purchase->type_doc);
                $worksheet->getCell('D' . $cell)->setValue($purchase->number_doc);
                $worksheet->getCell('E' . $cell)->setValue($purchase->observation);
                $worksheet->getCell('F' . $cell)->setValue($purchase->provider->name);
                $worksheet->getCell('G' . $cell)->setValue($purchase->total);

                $cell = $cell + 1;
                $x = $x + $purchase->total;
            }

            $worksheet->getCell('F' . $cell)->setValue('TOTAL');
            $worksheet->getCell('G' . $cell)->setValue(sprintf('%.2f', round($x, 2)));

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/purchases/Compra_" . date('d-m-Y', strtotime($request->date)) . '.xlsx'); //la salida

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/purchases/Compra_" . date('d-m-Y', strtotime($request->date)) . '.xlsx'
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
            $path_real = 'excel/PurchaseDateToDate.xlsx';
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
            $Purchase = Purchase::where('status', 1)->whereBetween('date', $request->date)->where('id_storage', $id_storage)->orderBy('date')->get();
            $storage = Storage::findOrFail($id_storage);
            if ($Purchase == null || $Purchase->count() == 0) {
                return response()->json([
                    'message' => 'No existe compras con ese rango de fechas'
                ], 400);
            }

            $worksheet->getCell('B7')->setValue('COMPRAS ENTRE FECHAS ' . date('d/m/Y', strtotime($request->date[0])) . ' - ' . date('d/m/Y', strtotime($request->date[1])) . ' (' . $storage->name . ')');

            $x = 0;
            $cell = 9;
            foreach ($Purchase as $purchase) {
                $worksheet->getCell('B' . $cell)->setValue(date('d/m/Y', strtotime($purchase->date)));
                $worksheet->getCell('C' . $cell)->setValue($purchase->type_doc);
                $worksheet->getCell('D' . $cell)->setValue($purchase->number_doc);
                $worksheet->getCell('E' . $cell)->setValue($purchase->observation);
                $worksheet->getCell('F' . $cell)->setValue($purchase->provider->name);
                $worksheet->getCell('G' . $cell)->setValue($purchase->total);

                $cell = $cell + 1;
                $x = $x + $purchase->total;
            }

            $worksheet->getCell('F' . $cell)->setValue('TOTAL');
            $worksheet->getCell('G' . $cell)->setValue(sprintf('%.2f', round($x, 2)));

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/purchases/Compra_Rango_" . date('d-m-Y', strtotime($request->date[0])) . '_a_' . date('d-m-Y', strtotime($request->date[1])) . '.xlsx');

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/purchases/Compra_Rango_" . date('d-m-Y', strtotime($request->date[0])) . '_a_' . date('d-m-Y', strtotime($request->date[1])) . '.xlsx'
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
            $path_real = 'excel/PurchaseForMonth.xlsx';
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
            $Purchase = Purchase::where('status', 1)->whereMonth('date', $request->date)->whereYear('date', $year)->where('id_storage', $id_storage)->orderBy('date')->get();
            $storage = Storage::findOrFail($id_storage);
            if ($Purchase == null || $Purchase->count() == 0) {
                return response()->json([
                    'message' => 'No existe compras en ese mes'
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

            $worksheet->getCell('B7')->setValue('COMPRAS DEL MES DE ' . $request->date . ' DEl ' . $year . ' (' . $storage->name . ')');

            $x = 0;
            $cell = 9;
            foreach ($Purchase as $purchase) {
                $worksheet->getCell('B' . $cell)->setValue(date('d/m/Y', strtotime($purchase->date)));
                $worksheet->getCell('C' . $cell)->setValue($purchase->type_doc);
                $worksheet->getCell('D' . $cell)->setValue($purchase->number_doc);
                $worksheet->getCell('E' . $cell)->setValue($purchase->observation);
                $worksheet->getCell('F' . $cell)->setValue($purchase->provider->name);
                $worksheet->getCell('G' . $cell)->setValue($purchase->total);

                $cell = $cell + 1;
                $x = $x + $purchase->total;
            }

            $worksheet->getCell('F' . $cell)->setValue('TOTAL');
            $worksheet->getCell('G' . $cell)->setValue(sprintf('%.2f', round($x, 2)));

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save("storage/purchases/Compra_Mes_" . $request->date . '-' . $year . '.xlsx');

            return response()->json([
                'message' => 'Exportado correctamente',
                'data' => URL::to('/') . "/storage/purchases/Compra_Mes_" . $request->date . '-' . $year . '.xlsx'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Excepcion ' . $e->getMessage()
            ],  500);
        }
    }

    public function totalForMonth(Request $request)
    {

        // $year = date("Y");

        // if (Auth::user()->id_role == 2) {
        //     // return ResourcesPurchase::collection(Purchase::where('id_storage', '1')->where('status', '1')->orderBy('date')->get());
        //    return DB::select("select DATE_FORMAT(DATE, '%M', 'es_ES')  as month,SUM(total) as total from purchases where id_storage='1' YEAR(DATE)=".$year." and status=1 GROUP BY month  ORDER BY date ASC");

        // }

        // if (Auth::user()->id_role == 3) {
        //     // return ResourcesPurchase::collection(Purchase::where('id_storage', '2')->where('status', '1')->orderBy('date')->get());
        //    return DB::select("select DATE_FORMAT(DATE, '%M', 'es_ES')  as month,SUM(total) as total from purchases where id_storage='2' YEAR(DATE)=".$year." and status=1 GROUP BY month  ORDER BY date ASC");

        // }

        // if (Auth::user()->id_role == 1) {
        //    // return ResourcesPurchase::collection(Purchase::where('status', '1')->orderBy('date')->get());
        //    return DB::select("select DATE_FORMAT(DATE, '%M', 'es_ES')  as month,SUM(total) as total from purchases where  YEAR(DATE)=".$year." and status=1 GROUP BY month  ORDER BY date ASC");

        // }


        $year = date("Y");

        if (Auth::user()->id_role == 2) {
            // return ResourcesPurchase::collection(Purchase::where('id_storage', '1')->where('status', '1')->orderBy('date')->get());
            DB::select("SET lc_time_names = 'es_ES';");
            return DB::select("select DATE_FORMAT(date, '%M %Y')  as month,SUM(total) as total from purchases where id_storage='1' and YEAR(date)=" . $year . " and status=1 GROUP BY MONTH(date),month  ORDER BY MONTH(date) ASC LIMIT 12");
        }

        if (Auth::user()->id_role == 3) {
            DB::select("SET lc_time_names = 'es_ES';");

            // return ResourcesPurchase::collection(Purchase::where('id_storage', '2')->where('status', '1')->orderBy('date')->get());
            return DB::select("select DATE_FORMAT(date, '%M %Y')  as month,SUM(total) as total from purchases where id_storage='2'and YEAR(date)=" . $year . " and status=1 GROUP BY MONTH(date),month  ORDER BY MONTH(date) ASC LIMIT 12");
        }

        if (Auth::user()->id_role == 1) {
            // return ResourcesPurchase::collection(Purchase::where('status', '1')->orderBy('date')->get());
            DB::select("SET lc_time_names = 'es_ES';");
            return DB::select("select DATE_FORMAT(date, '%M %Y')  as month,SUM(total) as total from purchases where  YEAR(date)=" . $year . " and status=1 GROUP BY MONTH(date),month  ORDER BY MONTH(date) ASC LIMIT 12");
        }
    }
}
