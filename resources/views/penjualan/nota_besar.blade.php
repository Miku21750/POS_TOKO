<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota PDF</title>

    <style>
        table td {
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 14px;
        }
        table.data td,
        table.data th {
            border: 1px solid #ccc;
            padding: 5px;
        }
        table.data {
            border-collapse: collapse;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        tfoot td {
            border: none!important;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td rowspan="4" width="60%">
                <img src="{{ public_path($setting->path_logo) }}" alt="{{ $setting->path_logo }}" width="120">
                <br>
                {{ $setting->alamat }}
                <br>
                <br>
            </td>
            <td>Tanggal</td>
            <td>: {{ tanggal_indonesia($penjualan->created_at) }}</td>
        </tr>
        <tr>
            <td>No Nota</td>
            <td>: {{ tambah_nol_didepan($penjualan->id_penjualan, 10) }}</td>
        </tr>
        <tr>
            <td>Nama Member</td>
            <td>: {{ $penjualan->member->nama ?? 'Pelanggan ................................................................' }}</td>
        </tr>
    </table>

    <table class="data" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Diskon</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $key => $item)
                <tr>
                    <td class="text-center">{{ $key+1 }}</td>
                    <td>{{ $item->produk->kode_produk }}</td>
                    <td>{{ $item->produk->nama_produk }}
                        @if ($item->serial_number != '0')
                            , SN : {{$item->serial_number}}
                        @endif
                    </td>
                    <td class="text-right">
                        {{-- {{ format_uang($item->harga_jual) }} --}}
                        @if ($item->subtotal == 0)
                            {{format_uang(0)}}
                        @else
                            {{ format_uang($item->harga_jual) }}
                        @endif
                    </td>
                    <td class="text-right">{{ format_uang($item->jumlah) }}</td>
                    <td class="text-right">
                        {{-- {{ $item->diskon }} --}}
                        {{-- ($diskon / 100 * $total) - (int)$potongan --}}
                        @if ($item->subtotal == 0)
                            {{format_uang(0)}}
                        @else
                            {{format_uang(($item->diskon / 100 * $item->harga_jual) + $item->nego)}}
                        @endif
                    </td>
                    <td class="text-right">{{ format_uang($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <td style='border:1px solid black; padding:5px; text-align:left; width:30%'> Keterangan : {{$penjualan->ket}}</td>
            <tr>
                <td colspan="6" class="text-right"><b>Total Harga</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->total_harga) }}</b></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><b>Diskon</b></td>
                <td class="text-right">
                    <b>
                        {{ format_uang(($penjualan->diskon / 100 * $penjualan->total_harga) + $penjualan->potongan) }}
                    </b>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><b>Total</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->bayar) }}</b></td>
            </tr>
            @if ($penjualan->payment == 'qriscash' || $penjualan->payment == 'debitcash' || $penjualan->payment == 'briscash')
                @switch($penjualan->payment)
                    @case('qriscash')
                        <tr>
                            <td colspan="6" class="text-right"><b>Metode Pembayaran</b></td>
                            <td class="text-right"><b>QRIS + Cash</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><b>Total Bayar</b></td>
                            <td class="text-right"><b>{{ format_uang($penjualan->diterima) }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-right"><b>Cash</b></td>
                            <td class="text-right"><b>{{ format_uang($penjualan->cash) }}</b></td>
                        </tr>
                        @break

                    @default

                @endswitch
            @else
                <tr>
                    <td colspan="6" class="text-right"><b>Metode Pembayaran</b></td>
                    <td class="text-right"><b>{{ $penjualan->payment }}</b></td>
                </tr>
                <tr>
                    <td colspan="6" class="text-right"><b>Total Bayar</b></td>
                    <td class="text-right"><b>{{ format_uang($penjualan->bayar) }}</b></td>
                </tr>
            @endif
            {{-- <tr>
                <td colspan="6" class="text-right"><b>Diterima</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima) }}</b></td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><b>Kembali</b></td>
                <td class="text-right"><b>{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</b></td>
            </tr> --}}
        </tfoot>
    </table>
    {{-- <table style='width:100%; font-size:8pt; font-family:calibri;  border: 1px solid;'>
        <tr>
            <td colspan="4" style="border:1px solid;">
                <div style="display:grid; text-align: right"><b>Total Harga</b></div>
            </td>
            <td colspan="2" style="text-align: right; border:1px solid;"><b>{{ format_uang($penjualan->total_harga) }}</b></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;">
                <div style="text-align: right"><b>Diskon</b></div>
            </td>
            <td colspan="2" style="text-align: right; border:1px solid;"><b>{{ format_uang($penjualan->diskon) }}</b></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;">
                <div style="text-align: right"><b>Total Bayar</b></div>
            </td>
            <td colspan="2" style="text-align: right; border:1px solid;"><b>{{ format_uang($penjualan->bayar) }}</b></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;">
                <div style="text-align: right"><b>Diterdsima</b></div>
            </td>
            <td colspan="2" style="text-align: right; border:1px solid;"><b>{{ format_uang($penjualan->diterima) }}</b></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;">
                <div style="text-align: right"><b>Kembali</b></div>
            </td>
            <td colspan="2" style="text-align: right; border:1px solid;"><b>{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</b></td>
        </tr>
    </table> --}}

    <table width="100%">
        <tr>
            <td><b>Barang yang sudah dibeli tidak dapat dikembalikan</b></td>
            <td class="text-center">
                Kasir
                <br>
                <br>
                {{ auth()->user()->name }}
            </td>
        </tr>
    </table>
</body>
</html>
