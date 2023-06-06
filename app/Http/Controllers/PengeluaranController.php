<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index()
    {
        return view('pengeluaran.index');
    }

    public function data()
    {
        $no = 1;
        $data = array();
        $total_kas = 0;
        // sedang mengambil data pengeluaran
        $pengeluaran = Pengeluaran::orderBy('created_at', 'desc')->get();
        foreach($pengeluaran as $pg){
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['created_at'] = tanggal_indonesia($pg->created_at, false);
            $row['deskripsi'] = $pg->deskripsi;
            $nominal = '';
            if($pg->nominal < 0){
                $nominal = '+'.format_uang($pg->nominal * -1);
            }else{
                $nominal = '-'.format_uang($pg->nominal);
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
            'nominal' => format_uang($total_kas),
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
