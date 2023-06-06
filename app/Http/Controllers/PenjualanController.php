<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;
use PDF;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('penjualan.index');
    }

    public function data()
    {
        $penjualan = Penjualan::with('member')->orderBy('id_penjualan', 'desc')->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('kode_member', function ($penjualan) {
                $member = $penjualan->member->kode_member ?? '';
                return '<span class="label label-success">'. $member .'</spa>';
            })
            ->editColumn('diskon', function ($penjualan) {
                return 'Rp. ' . format_uang(($penjualan->diskon / 100 * $penjualan->total_harga) + $penjualan->potongan);
                // return $penjualan->diskon . '%';
                //($penjualan->diskon / 100 * $penjualan->total_harga) + $penjualan->potongan
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            ->editColumn('payment', function ($penjualan) {
                return $penjualan->payment;
            })
            ->addColumn('ket', function ($penjualan) {
                if($penjualan->payment == 'qriscash' || $penjualan->payment == 'debitcash' || $penjualan->payment == 'briscash' || $penjualan->payment == 'tfcash'){
                    return 'Diterima : ' . $penjualan->diterima . ', Cash : ' . $penjualan->cash . ', Ket : ' . $penjualan->ket ?? '';
                }else{
                    return $penjualan->ket ?? '';
                }
            })
            ->addColumn('aksi', function ($penjualan) {
                //"notaBesar('{{ route('transaksi.nota_besar') }}', 'Nota PDF'
                return '
                <div class="btn-group">
                    <button onclick="notaBesar(`'. route('transaksi.nota_besar_ulang',  $penjualan->id_penjualan) .'`,`Nota PDF`)" class="btn btn-xs btn-success btn-flat"><i class="fa fa-print"></i></button>
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_member'])
            ->make(true);
    }

    public function create()
    {
        $penjualan = new Penjualan();
        $penjualan->id_member = null;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->potongan = 0;
        $penjualan->ppn = 0;
        $penjualan->bayar = 0;
        $penjualan->payment = 'cash';
        $penjualan->diterima = 0;
        $penjualan->cash = 0;
        $penjualan->id_user = auth()->id();
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        // return var_dump($request->date);

        $kembali = $request->kembali;
        $diterima = $request->diterima;
        $bayar = $request->bayar;
        $penjualan = Penjualan::findOrFail($request->id_penjualan);
        $penjualan->id_member = $request->id_member;
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        // $diskon = $request->diskon;
        $penjualan->diskon = $request->diskon;
        $penjualan->potongan = $request->potongan ?? 0;
        $penjualan->bayar = $request->bayar;
        $penjualan->ppn = $request->ppn ?? 0;
        $penjualan->payment = $request->payment ?? "cash";
        $penjualan->diterima = $request->diterima;
        $penjualan->cash = $request->cash ?? 0;
        $penjualan->kembali = $request->kembali;
        $penjualan->harga_final = $bayar;
        $penjualan->ket = $request->ket;
        $penjualan->created_at = $request->date;
        // if($diterima >= $bayar){
        //     $penjualan->harga_final = $bayar;
        // }else{
        //     $penjualan->harga_final = $diterima;
        // }
        $penjualan->update();

        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $item->diskon = $request->diskon;
            $item->update();

            $produk = Produk::find($item->id_produk);
            $produk->stok -= $item->jumlah;
            $produk->update();
        }

        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                if($detail->serial_number != '0'){
                    return $detail->produk->nama_produk . ', SN : ' . $detail->serial_number;
                }else{
                    return $detail->produk->nama_produk;
                }

            })
            ->addColumn('harga_jual', function ($detail) {
                return 'Rp. '. format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('diskon', function ($detail) {
                //($item->diskon / 100 * $item->harga_jual) + $item->nego
                return format_uang(($detail->diskon / 100 * $detail->harga_jual) + $detail->nego);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. '. format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        $detail    = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }
    public function notaFaktur()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        return view('penjualan.nota_faktur', compact('setting', 'penjualan', 'detail'));
    }
    public function notaJalan()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        return view('penjualan.nota_jalan', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }
    public function notaBesarUlang($id)
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find($id);
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }
}
