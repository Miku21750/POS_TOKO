<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        $member = Member::orderBy('nama')->get();
        $diskon = Setting::first()->diskon ?? 0;
        // Cek apakah ada transaksi yang sedang berjalan
        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = Penjualan::find($id_penjualan);
            $memberSelected = $penjualan->member ?? new Member();

            return view('penjualan_detail.index', compact('produk', 'member', 'diskon', 'id_penjualan', 'penjualan', 'memberSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaksi.baru');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function data($id)
    {
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        // return var_dump($detail);
        $total = 0;
        $total_item = 0;
        // $bayar   = $total - ($diskon / 100 * $total);
        foreach ($detail as $item) {
            $row = array();
            $nego = $item->nego;
            // $subtotal = ($item->subtotal - ($item->diskon / 100 * $item->subtotal) - $nego);
            $subtotal = $item->subtotal;

            $row['kode_produk'] = '<span class="label label-success">'. $item->produk['kode_produk'] .'</span';
            $row['nama_produk'] = $item->produk['nama_produk'];
            $row['harga_jual']  = 'Rp. '. format_uang($item->harga_jual);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_penjualan_detail .'" value="'. $item->jumlah .'">';
            $row['diskon']      = $item->diskon . '%';
            // $row['subtotal']    = 'Rp. '. format_uang($subtotal);
            $row['nego']    = '<input type="number" class="form-control input-sm nego" data-id="'. $item->id_penjualan_detail .'" value="'. $item->nego .'">';
            $row['subtotal']    = $item->subtotal;
            $row['sn']    = '<input type="text" class="form-control input-sm sn" data-id="'. $item->id_penjualan_detail .'" value="'. $item->serial_number.'">';
            // $row['sn']    = $item->serial_number;
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaksi.destroy', $item->id_penjualan_detail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            // $total += $subtotal * $item->jumlah;
            $total += $subtotal;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'kode_produk' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'nama_produk' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'diskon'      => '',
            'nego'      => '',
            'subtotal'    => '',
            'sn'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah','subtotal','sn','nego'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::where('id_produk', $request->id_produk)->first();
        if (! $produk) {
            return response()->json('Data gagal disimpan', 400);
        }

        $detail = new PenjualanDetail();
        $detail->id_penjualan = $request->id_penjualan;
        $detail->id_produk = $produk->id_produk;
        $detail->harga_jual = $produk->harga_jual;
        $detail->jumlah = 1;
        // $diskon = $produk->diskon / 100 * $produk->harga_jual;
        $detail->diskon = $produk->diskon;
        $detail->subtotal = $produk->harga_jual;
        $detail->serial_number = 0;
        $detail->nego = 0;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        // return var_dump($request);
        $detail = PenjualanDetail::find($id);
        $jumlah =  $request->jumlah ?? $detail->jumlah;
        $nego = $request->nego ?? $detail->nego;
        $diskon = $detail->diskon;
        $detail->jumlah = $jumlah;
        $detail->subtotal = ($detail->harga_jual * (int)$jumlah) - ($diskon / 100 * ($detail->harga_jual * (int)$jumlah)) - (int)$nego;
        // $detail->subtotal = $detail->subtotal ;
        $detail->serial_number = $request->sn ?? $detail->serial_number;
        $detail->nego = $request->nego ?? $detail->nego;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = PenjualanDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon, $total = 0, $diterima = 0, $ppn, $potongan,$payment,$cash)
    {
        // return var_dump($potongan);
        $pajak = 0;
        if($ppn != 0){
            $bayartmp   = $total - ($diskon / 100 * $total) - (int)$potongan;
            $pajak = 11/100 * $bayartmp;
            $bayar = $bayartmp + $pajak;
        }else{
            $bayar   = $total - ($diskon / 100 * $total) - (int)$potongan;
        }

        $kembali = ((int)$diterima != 0) ? ((int)$diterima + (int)$cash) - $bayar : 0;
        $data    = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'pajak' => format_uang($pajak),
            'bayarrp' => format_uang($bayar),
            'kembali' => $kembali,
            'terbilang' => ucwords(terbilang($bayar). ' Rupiah'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali). ' Rupiah'),
        ];

        return response()->json($data);
    }
}
