<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        global $tglAwal, $tglAkhir;
        $tglAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tglAkhir = date('Y-m-d');
        // return var_dump($tanggalAkhir, $tanggalAwal);
        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tglAwal = $request->tanggal_awal;
            $tglAkhir = $request->tanggal_akhir;
        }
        return view('pengeluaran.index', compact('tglAwal', 'tglAkhir'));
    }

    public function data($awal, $akhir)
    {
        $no = 1;
        $data = array();
        // sedang mengambil data pengeluaran
        $awal = date($awal);
        $akhir = date('Y-m-d', strtotime($akhir . "+1 days"));
        // Pengeluaran::enableQueryLog();
        $kasbefore = Pengeluaran::where('created_at','<',$awal)->orderBy('created_at', 'asc')->sum('nominal');
        $kasNom = '';
        if($kasbefore < 0){
            $kasNom = '+'.format_uang($kasbefore * -1);
        }else{
            $kasNom = '-'.format_uang($kasbefore);
        }
        // dump(Pengeluaran::getQueryLog());
        $pengeluaran = Pengeluaran::whereBetween('created_at',[$awal,$akhir])->orderBy('created_at', 'asc')->get();
        $data[] = [
            'DT_RowIndex' => '',
            'created_at' => 'Kas Bulan Lalu',
            'deskripsi' => '',
            'nominal' => $kasNom,
            'aksi' => '',
        ];
        $total_kas = $kasbefore * -1;
        foreach($pengeluaran as $pg){
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['created_at'] = tanggal_indonesia($pg->created_at, false);
            $row['deskripsi'] = $pg->deskripsi;
            $nominal = '';
            if($pg->nominal < 0){
                $nominal = '+'.'Rp. '.format_uang($pg->nominal * -1);
            }else{
                $nominal = '-'.'Rp. '.format_uang($pg->nominal);
            }
            $row['nominal'] = $nominal;
            $row['aksi'] = '
            <div class="btn-group">
                <button type="button" onclick="editForm(`'. route('pengeluaran.update', $pg->id_pengeluaran) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                <button type="button" onclick="deleteData(`'. route('pengeluaran.destroy', $pg->id_pengeluaran) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
            </div>';

            $data[] = $row;
            $total_kas -= $pg->nominal;
        }

        $data[] = [
            'DT_RowIndex' => '',
            'created_at' => 'Total Kas',
            'deskripsi' => '',
            'nominal' => 'Rp. '.format_uang($total_kas),
            'aksi' => '',
        ];

        return datatables()->of($data)->rawColumns(['aksi'])->make(true);

        // return datatables()
        //     ->of($pengeluaran)
        //     ->addIndexColumn()
        //     ->addColumn('created_at', function ($pengeluaran) {
        //         return tanggal_indonesia($pengeluaran->created_at, false);
        //     })
        //     ->addColumn('nominal', function ($pengeluaran) {
        //         return format_uang($pengeluaran->nominal);
        //     })
        //     ->addColumn('aksi', function ($pengeluaran) {
        //         return '
        //         <div class="btn-group">
        //             <button type="button" onclick="editForm(`'. route('pengeluaran.update', $pengeluaran->id_pengeluaran) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
        //             <button type="button" onclick="deleteData(`'. route('pengeluaran.destroy', $pengeluaran->id_pengeluaran) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
        //         </div>
        //         ';
        //     })
        //     ->rawColumns(['aksi'])
        //     ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pengeluaran = Pengeluaran::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pengeluaran = Pengeluaran::find($id);

        return response()->json($pengeluaran);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::find($id)->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::find($id)->delete();

        return response(null, 204);
    }
}
