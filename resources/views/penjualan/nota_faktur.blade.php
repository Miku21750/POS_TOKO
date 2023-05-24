<html>

<head>
    <title>Faktur Pembayaran</title>
    <style>
        #tabel {
            font-size: 15px;
            border-collapse: collapse;
        }

        #tabel td {
            padding-left: 5px;
            border: 1px solid black;
        }
    </style>
</head>

<body style='font-family:tahoma; font-size:8pt;' onload="window.print()">
    <center>
        <table style='width:550px; font-size:8pt; font-family:calibri; border-collapse: collapse;' border='0'>
            <td width='70%' align='left' style='padding-right:80px; vertical-align:top'>
                <span style='font-size:12pt'><b>CV Cita Adi Karya</b></span></br>Perum Regency No 1 Pakembaran Slawi Kab. Tegal</br>
                Telp : 0283-492086
            </td>
            <td style='vertical-align:top' width='30%' align='left'>
                <b><span style='font-size:12pt'>FAKTUR PENJUALAN</span></b></br>
                No Nota : {{ tambah_nol_didepan($penjualan->id_penjualan, 10) }}</br>
                Tanggal : {{ date('d-m-Y') }}</br>
            </td>
        </table>
        <table style='width:550px; font-size:8pt; font-family:calibri; border-collapse: collapse;' border='0'>
            <td width='70%' align='left' style='padding-right:80px; vertical-align:top'>
                Nama Pelanggan :{{$penjualan->member->nama ?? 'Pelanggan ................................................................'}}</br>
                Alamat : {{$penjualan->member->alamat ?? '................................................................'}}
            </td>
            <td style='vertical-align:top' width='30%' align='left'>
                No Telp : {{$penjualan->member->telepon ?? '................................................................'}}
            </td>
        </table>
        <table cellspacing='0' style='width:550px; font-size:8pt; font-family:calibri;  border-collapse: collapse;'
            border='1'>

            <tr align='center'>
                <td width='10%'>NO</td>
                <td width='12%'>Kode Barang</td>
                <td width='20%'>Nama Barang</td>
                <td width='13%'>Harga Satuan</td>
                <td width='4%'>Qty</td>
                <td width='7%'>Discount</td>
                <td width='13%'>Total Harga</td>
            </tr>
            @php
                $detailNumber = 1;
            @endphp
            @foreach ($detail as $item)
                <tr>
                    {{-- <td>{{$detailNumber}}</td>
                    <td>{{ $item->produk->nama_produk }}  {{ $item->jumlah }} x {{ format_uang($item->harga_jual) }}</td>

                    <td>{{ format_uang($item->jumlah * $item->harga_jual) }}</td> --}}
                    <td>{{$detailNumber}}</td>
                    <td>{{ $item->produk->kode_produk }}</td>
                    <td>{{ $item->produk->nama_produk }}
                        @if ($item->serial_number != '0')
                            , SN : {{$item->serial_number}}
                        @endif
                    </td>
                    <td>
                        {{-- {{ format_uang($item->harga_jual) }} --}}
                        @if ($item->subtotal == 0)
                            {{format_uang(0)}}
                        @else
                            {{ format_uang($item->harga_jual) }}
                        @endif
                    </td>
                    <td>{{ $item->jumlah }}</td>
                    <td>
                        {{-- {{ $item->diskon }} --}}
                        {{-- ($diskon / 100 * $total) - (int)$potongan --}}
                        @if ($item->subtotal == 0)
                            {{format_uang(0)}}
                        @else
                            {{format_uang(($item->diskon / 100 * $item->harga_jual) + $item->nego)}}
                        @endif
                    </td>
                    <td style='text-align:right'>{{ format_uang($item->subtotal) }}</td>
                </tr>
                @php
                    $detailNumber += 1;
                @endphp
            @endforeach
                <table cellspacing='0' style='width:550px; font-size:8pt; font-family:calibri;  border: 0;'>
                    <tr>
                        <td colspan='5'>
                            <div style='text-align:right'>PPN : </div>
                        </td>
                        {{-- <td style='text-align:right'>{{ format_uang($penjualan->bayar) }}</td> --}}
                        <td style='text-align:right'>{{ format_uang(0) }}</td>
                    </tr>
                    <tr>
                        <td colspan='5'>
                            <div style='text-align:right'>Total Yang Harus Di Bayar Adalah : </div>
                        </td>
                        <td style='text-align:right'>{{ format_uang($penjualan->bayar) }}</td>
                    </tr>
                    <tr>
                        <td colspan='6'>
                            <div style='text-align:right'>Terbilang :  {{ucwords(terbilang($penjualan->bayar). ' Rupiah')}}</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='5'>
                            <div style='text-align:right'>Cash : </div>
                        </td>
                        <td style='text-align:right'>{{ format_uang($penjualan->diterima) }}</td>
                    </tr>
                    <tr>
                        <td colspan='5'>
                            @if (($penjualan->diterima - $penjualan->bayar) < 0)
                                <div style='text-align:right'>Sisa : </div>
                            @else
                                <div style='text-align:right'>Kembalian : </div>
                            @endif
                        </td>
                        <td style='text-align:right'>{{ format_uang(abs($penjualan->diterima - $penjualan->bayar)) }}</td>
                    </tr>
                </table>
            {{-- <tr>
                <td colspan='5'>
                    <div style='text-align:right'>PPN : </div>
                </td>
                <td style='text-align:right'>Rp0,00</td>
            </tr>
            <tr>
                <td colspan='5'>
                    <div style='text-align:right'>Sisa : </div>
                </td>
                <td style='text-align:right'>Rp0,00</td>
            </tr> --}}
        </table>

        <table style='width:650; font-size:7pt;' cellspacing='2'>
            <tr>
                <td align='center'>Diterima Oleh,</br></br><u>(............)</u></td>
                <td style='border:1px solid black; padding:5px; text-align:left; width:30%'></td>
                <td align='center'>TTD,</br></br><u>(...........)</u></td>
            </tr>
        </table>
    </center>
    {{-- <p>{{$penjualan->member}}</p> --}}
</body>

</html>
