<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use Illuminate\Http\Request;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            global $tanggalAwal, $tanggalAkhir;
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }
    public function index2(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            global $tanggalAwal, $tanggalAkhir;
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.peripheral', compact('tanggalAwal', 'tanggalAkhir'));
    }
    public function index3(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            global $tanggalAwal, $tanggalAkhir;
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.jasa', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;
        $this->tanggalAwal = date($awal);
        $this->tanggalAkhir = date('Y-m-d', strtotime($akhir . "+1 days"));
        // return var_dump($akhir);
        $penj = Penjualan::join(
            "penjualan_detail",
            function ($join) {
                $join->on("penjualan.id_penjualan", "=", "penjualan_detail.id_penjualan")
                // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                ->whereBetween("penjualan.created_at", [$this->tanggalAwal, $this->tanggalAkhir]);
            }
            )->join("produk", function ($join) {
                // return var_dump($this);
                $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                ->whereRaw("(produk.id_kategori = ? OR produk.id_kategori = ?) AND (penjualan.id_member != 3 OR penjualan.id_member IS NULL)", array(1, 37));
                // ->where("produk.id_kategori",1)->orWhere('id_kategori',37);
            })
            ->select("penjualan.id_penjualan", "produk.nama_produk", "produk.harga_beli", "penjualan_detail.subtotal", "penjualan_detail.jumlah", "penjualan.payment", "penjualan.diterima", "penjualan.cash", "penjualan.ket", "penjualan.created_at")
            // ->where("penjualan.id_penjualan",$p->id_penjualan)
            ->get();
            foreach ($penj as $p) {
                // return var_dump($p->id_penjualan);
                $tanggal = $awal;
            // global $kuda;
            // $kuda = $awal;
            // return var_dump($this);
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            // $total_pembayaran = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('diterima');
            // $total_pengembalian = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('kembali');
            // $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->where()->sum('harga_final');
            // return var_dump($awal, $akhir);
            // $produk = Produk::where('id_kategori',1)->orWhere('id_kategori',37);
            // $penjualan_detail = PenjualanDetail::join('produk', 'penjualan_detail.id_produk','=','produk.id_produk')->where('id_kategori',1)->orWhere('id_kategori',37)->get();

            $penjualan = Penjualan::join(
                "penjualan_detail",
                function ($join) {
                    $join->on("penjualan.id_penjualan", "=", "penjualan_detail.id_penjualan")
                    // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                    ->whereBetween("penjualan.created_at", [$this->tanggalAwal, $this->tanggalAkhir]);
                }
                )->join("produk", function ($join) {
                    // return var_dump($this);
                    $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                    // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                    ->whereRaw("(produk.id_kategori = ? OR produk.id_kategori = ?)  AND (penjualan.id_member != 3 OR penjualan.id_member IS NULL)", array(1, 37));
                // ->where("produk.id_kategori",1)->orWhere('id_kategori',37);
            })
            ->select("penjualan.id_penjualan", "produk.nama_produk", "produk.harga_beli", "penjualan_detail.subtotal", "penjualan.payment", "penjualan.diterima", "penjualan.cash", "penjualan.ket", "penjualan.created_at")
            ->where("penjualan.id_penjualan", $p->id_penjualan)
            ->get();
            foreach ($penjualan as $pd) {
                // return var_dump($pd->payment);
                $this->id_penjualan = $pd->id_penjualan;
                $txt = '';
                $penjDetailLaptop = PenjualanDetail::join('produk', function ($join) {
                    $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                    ->where("penjualan_detail.id_penjualan", "=", $this->id_penjualan)
                    ->whereIn('produk.id_kategori', [1, 37]);
                })
                ->get();
                // return var_dump($penjDetailLaptop);
                if ($penjDetailLaptop->count() != 0) {
                    foreach ($penjDetailLaptop as $ppd) {
                        // return var_dump(defined($p->nama_produk));
                        $txt = $txt . 'SN: ' . $ppd->serial_number . ',';
                    }
                }
                $txt = $txt . 'Pembayaran : ' . $pd->payment;
                if ($pd->payment == 'qriscash' || $pd->payment == 'briscash' || $pd->payment == 'debitcash' || $pd->payment == 'tfcash') {
                    $txt = $txt . ",Diterima : " . $pd->diterima . ",Cash : " . $pd->cash;
                }
                // $penjDetail = PenjualanDetail::where('id_penjualan',$pd->id_penjualan)->get();
                $penjDetail = PenjualanDetail::join('produk', function ($join) {
                    $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                    ->where("penjualan_detail.id_penjualan", "=", $this->id_penjualan)
                    ->whereNotIn('produk.id_kategori', [1, 37, 36]);
                })
                ->get();
                // return var_dump($penjDetail->count() != 0);
                if ($penjDetail->count() != 0) {
                    $txt = $txt . ",Bonus :";
                    foreach ($penjDetail as $ppd) {
                        // return var_dump(defined($p->nama_produk));
                        $txt = $txt . ' ' . $ppd->nama_produk . ',';
                    }
                }
                // var_dump($p->nama_produk);
                $txt = $txt . ",Ket : " . $pd->ket;
                // return var_dump($txt);
            }
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($p->created_at, false);
            $row['nama_produk'] = $p->nama_produk;
            $row['jumlah'] = $p->jumlah;
            $row['harga_jual'] = format_uang($p->subtotal);
            $row['harga_beli'] = format_uang($p->harga_beli);
            $row['margin'] = format_uang($p->subtotal - $pd->harga_beli);
            $row['no_nota'] = tambah_nol_didepan($p->id_penjualan, 10);
            $row['ket'] = $txt;

            $data[] = $row;
            $pendapatan = $p->subtotal - $p->harga_beli;
            $total_pendapatan += $pendapatan;

            // // return var_dump($penjualan);
            // $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('harga_final');
            // // var_dump($total_penjualan);
            // $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            // $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            // $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            // $total_pendapatan += $pendapatan;



        }
        // return var_dump($penj);
        // return var_dump($tanggalAwal,$tanggalAkhir);
        // while (strtotime($awal) <= strtotime($akhir)) {


        // }

        // return var_dump($this);


        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'nama_produk' => '',
            'jumlah' => '',
            'harga_jual' => '',
            'harga_beli' => 'Total Pendapatan',
            'margin' => format_uang($total_pendapatan),
            'no_nota' => '',
            'ket' => '',
            // 'pendapatan' => format_uang($total_pendapatan),
        ];

        return $data;
    }
    public function getData_peripheral($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;
        $this->tanggalAwal = date($awal);
        $this->tanggalAkhir = date('Y-m-d', strtotime($akhir . "+1 days"));
        // return var_dump($akhir);
        $penj = Penjualan::join(
            "penjualan_detail",
            function ($join) {
                $join->on("penjualan.id_penjualan", "=", "penjualan_detail.id_penjualan")
                    // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                    ->whereBetween("penjualan.created_at", [$this->tanggalAwal, $this->tanggalAkhir]);
            }
        )->join("produk", function ($join) {
            // return var_dump($this);
            $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                ->whereRaw("NOT (produk.id_kategori = ? OR produk.id_kategori = ? OR produk.id_kategori = ?)  AND (penjualan.id_member != 3 OR penjualan.id_member IS NULL)", array(1, 36, 37));
            // ->where("produk.id_kategori",1)->orWhere('id_kategori',37);
        })
            ->select("penjualan.id_penjualan", "produk.nama_produk", "produk.harga_beli", "penjualan_detail.subtotal", "penjualan_detail.jumlah", "penjualan.payment", "penjualan.diterima", "penjualan.cash", "penjualan.ket", "penjualan.created_at")
            // ->where("penjualan.id_penjualan",$p->id_penjualan)
            ->get();
        foreach ($penj as $p) {
            // return var_dump($p->id_penjualan);
            $tanggal = $awal;
            // global $kuda;
            // $kuda = $awal;
            // return var_dump($this);
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            // $total_pembayaran = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('diterima');
            // $total_pengembalian = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('kembali');
            // $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->where()->sum('harga_final');
            // return var_dump($awal, $akhir);
            // $produk = Produk::where('id_kategori',1)->orWhere('id_kategori',37);
            // $penjualan_detail = PenjualanDetail::join('produk', 'penjualan_detail.id_produk','=','produk.id_produk')->where('id_kategori',1)->orWhere('id_kategori',37)->get();

            $penjualan = Penjualan::join(
                "penjualan_detail",
                function ($join) {
                    $join->on("penjualan.id_penjualan", "=", "penjualan_detail.id_penjualan")
                        // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                        ->whereBetween("penjualan.created_at", [$this->tanggalAwal, $this->tanggalAkhir]);
                }
            )->join("produk", function ($join) {
                // return var_dump($this);
                $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                    // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                    ->whereRaw("NOT (produk.id_kategori = ? OR produk.id_kategori = ? OR produk.id_kategori = ?) AND (penjualan.id_member != 3 OR penjualan.id_member IS NULL)", array(1, 36, 37));
                // ->where("produk.id_kategori",1)->orWhere('id_kategori',37);
            })
                ->select("penjualan.id_penjualan", "produk.nama_produk", "produk.harga_beli", "penjualan_detail.subtotal", "penjualan.payment", "penjualan.diterima", "penjualan.cash", "penjualan.ket", "penjualan.created_at")
                ->where("penjualan.id_penjualan", $p->id_penjualan)
                ->get();
            foreach ($penjualan as $pd) {
                // return var_dump($pd->id_penjualan);
                $this->id_penjualan = $pd->id_penjualan;
                $txt = '';
                if ($pd->subtotal - $pd->harga_beli < 0) {
                    $txt = $txt . 'Bonus Barang';
                    $penjDetail = PenjualanDetail::join('produk', function ($join) {
                        $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                            ->where("penjualan_detail.id_penjualan", "=", $this->id_penjualan)
                            ->where('produk.id_kategori', [1, 37]);
                    })
                        ->get();
                    $txt = $txt . ' Hasil Pembelian Laptop';
                    foreach ($penjDetail as $pD) {
                        switch ($pD->id_kategori) {
                            case 1: {
                                    $txt = $txt . ' ';
                                }
                                break;
                            case 37: {
                                    $txt = $txt . ' Second ';
                                }
                                break;
                        }
                        $txt = $txt . $pD->nama_produk;
                    }
                } else {
                    $txt = $txt . 'Pembayaran : ' . $pd->payment;
                    if ($pd->payment == 'qriscash' || $pd->payment == 'briscash' || $pd->payment == 'debitcash' || $pd->payment == 'tfcash') {
                        $txt = $txt . ",Diterima : " . $pd->diterima . ",Cash : " . $pd->cash;
                    }
                    // $penjDetail = PenjualanDetail::where('id_penjualan',$pd->id_penjualan)->get();
                    // return var_dump($penjDetail->count() != 0);
                    // if($penjDetail->count() != 0){
                    //     $txt = $txt . ",Bonus :";
                    //     foreach($penjDetail as $p){
                    //         // return var_dump(defined($p->nama_produk));
                    //         $txt = $txt . ' ' . $p->nama_produk . ',';
                    //     }
                    // }
                    $txt = $txt . ",Ket : " . $pd->ket;
                }
            }
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($p->created_at, false);
            $row['nama_produk'] = $p->nama_produk;
            $row['jumlah'] = $p->jumlah;
            $row['harga_jual'] = format_uang($p->subtotal);
            $row['harga_beli'] = format_uang($p->harga_beli);
            $row['margin'] = format_uang($p->subtotal - $p->harga_beli);
            $row['no_nota'] = tambah_nol_didepan($p->id_penjualan, 10);
            $row['ket'] = $txt;

            $data[] = $row;
            $pendapatan = $p->subtotal - $p->harga_beli;
            $total_pendapatan += $pendapatan;

            // // return var_dump($penjualan);
            // $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('harga_final');
            // // var_dump($total_penjualan);
            // $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            // $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            // $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            // $total_pendapatan += $pendapatan;



        }
        // return var_dump($penj);
        // return var_dump($tanggalAwal,$tanggalAkhir);
        // while (strtotime($awal) <= strtotime($akhir)) {


        // }

        // return var_dump($this);


        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'nama_produk' => '',
            'jumlah' => '',
            'harga_jual' => '',
            'harga_beli' => 'Total Pendapatan',
            'margin' => format_uang($total_pendapatan),
            'no_nota' => '',
            'ket' => '',
            // 'pendapatan' => format_uang($total_pendapatan),
        ];

        return $data;
    }
    public function getData_jasa($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;
        $this->tanggalAwal = date($awal);
        $this->tanggalAkhir = date('Y-m-d', strtotime($akhir . "+1 days"));
        // return var_dump($akhir);
        $penj = Penjualan::join(
            "penjualan_detail",
            function ($join) {
                $join->on("penjualan.id_penjualan", "=", "penjualan_detail.id_penjualan")
                    // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                    ->whereBetween("penjualan.created_at", [$this->tanggalAwal, $this->tanggalAkhir]);
            }
        )->join("produk", function ($join) {
            // return var_dump($this);
            $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                ->where("produk.id_kategori", "=", 36)
                ->whereRaw("(penjualan.id_member != 3 OR penjualan.id_member IS NULL)");
            // ->where("produk.id_kategori",1)->orWhere('id_kategori',37);
        })
            ->select("penjualan.id_penjualan", "produk.nama_produk", "produk.harga_beli", "penjualan_detail.subtotal", "penjualan.payment", "penjualan.diterima", "penjualan.cash", "penjualan.ket", "penjualan.created_at")
            // ->where("penjualan.id_penjualan",$p->id_penjualan)
            ->get();
        foreach ($penj as $p) {
            // return var_dump($p->id_penjualan);
            $tanggal = $awal;
            // global $kuda;
            // $kuda = $awal;
            // return var_dump($this);
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            // $total_pembayaran = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('diterima');
            // $total_pengembalian = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('kembali');
            // $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->where()->sum('harga_final');
            // return var_dump($awal, $akhir);
            // $produk = Produk::where('id_kategori',1)->orWhere('id_kategori',37);
            // $penjualan_detail = PenjualanDetail::join('produk', 'penjualan_detail.id_produk','=','produk.id_produk')->where('id_kategori',1)->orWhere('id_kategori',37)->get();

            $penjualan = Penjualan::join(
                "penjualan_detail",
                function ($join) {
                    $join->on("penjualan.id_penjualan", "=", "penjualan_detail.id_penjualan")
                        // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                        ->whereBetween("penjualan.created_at", [$this->tanggalAwal, $this->tanggalAkhir]);
                }
            )->join("produk", function ($join) {
                // return var_dump($this);
                $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                    // ->whereBetween(raw("( penjualan.created_at >= '2023-05-24' and penjualan.created_at < '2023-05-30' )"));
                    ->whereRaw("produk.id_kategori = ?", array(36));
                // ->where("produk.id_kategori",1)->orWhere('id_kategori',37);
            })
                ->select("penjualan.id_penjualan", "produk.nama_produk", "produk.harga_beli", "penjualan_detail.subtotal", "penjualan.payment", "penjualan.diterima", "penjualan.cash", "penjualan.ket", "penjualan.created_at")
                ->where("penjualan.id_penjualan", $p->id_penjualan)
                ->get();
            foreach ($penjualan as $pd) {
                // return var_dump($pd->id_penjualan);
                $this->id_penjualan = $pd->id_penjualan;
                $txt = '';
                $txt = $txt . 'Pembayaran : ' . $pd->payment;
                if ($pd->payment == 'qriscash' || $pd->payment == 'briscash' || $pd->payment == 'debitcash' || $pd->payment == 'tfcash') {
                    $txt = $txt . ",Diterima : " . $pd->diterima . ",Cash : " . $pd->cash;
                }
                // $penjDetail = PenjualanDetail::where('id_penjualan',$pd->id_penjualan)->get();
                $penjDetail = PenjualanDetail::join('produk', function ($join) {
                    $join->on("penjualan_detail.id_produk", "=", "produk.id_produk")
                        ->where("penjualan_detail.id_penjualan", "=", $this->id_penjualan)
                        ->whereNotIn('produk.id_kategori', [1, 37, 36]);
                })
                    ->get();
                // return var_dump($penjDetail->count() != 0);
                if ($penjDetail->count() != 0) {
                    $txt = $txt . ",Barang Sparepart :";
                    foreach ($penjDetail as $ppd) {
                        // return var_dump(defined($p->nama_produk));
                        $txt = $txt . ' ' . $ppd->nama_produk . ',';
                    }
                }
                $txt = $txt . ",Ket : " . $pd->ket;
                // return var_dump($txt);
            }
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($p->created_at, false);
            $row['nama_produk'] = $p->nama_produk;
            $row['harga_jual'] = format_uang($p->subtotal);
            $row['harga_beli'] = format_uang($p->harga_beli);
            $row['margin'] = format_uang($p->subtotal - $p->harga_beli);
            $row['no_nota'] = tambah_nol_didepan($p->id_penjualan, 10);
            $row['ket'] = $txt;

            $data[] = $row;
            $pendapatan = $p->subtotal - $p->harga_beli;
            $total_pendapatan += $pendapatan;

            // // return var_dump($penjualan);
            // $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('harga_final');
            // // var_dump($total_penjualan);
            // $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            // $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            // $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            // $total_pendapatan += $pendapatan;



        }
        // return var_dump($penj);
        // return var_dump($tanggalAwal,$tanggalAkhir);
        // while (strtotime($awal) <= strtotime($akhir)) {


        // }

        // return var_dump($this);


        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'nama_produk' => '',
            'harga_jual' => '',
            'harga_beli' => 'Total Pendapatan',
            'margin' => format_uang($total_pendapatan),
            'no_nota' => '',
            'ket' => '',
            // 'pendapatan' => format_uang($total_pendapatan),
        ];

        return $data;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }
    public function data_peripheral($awal, $akhir)
    {
        $data = $this->getData_peripheral($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }
    public function data_jasa($awal, $akhir)
    {
        $data = $this->getData_jasa($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);
        $pdf  = PDF::loadView('laporan.pdf', compact('awal', 'akhir', 'data'));
        $pdf->setPaper('a4', 'potrait');

        return $pdf->stream('Laporan-pendapatan-' . date('Y-m-d-his') . '.pdf');
    }
}
