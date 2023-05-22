<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Kecil</title>

    <?php
    $style = '
    <style>
        * {
            font-family: "consolas", sans-serif;
        }
        p {
            display: block;
            margin: 3px;
            font-size: 10pt;
        }
        table td {
            font-size: 9pt;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }

        @media print {
            @page {
                margin: 0;
                size: 75mm
    ';
    ?>
    <?php
    $style .=
        ! empty($_COOKIE['innerHeight'])
            ? $_COOKIE['innerHeight'] .'mm; }'
            : '}';
    ?>
    <?php
    $style .= '
            html, body {
                width: 70mm;
            }
            .btn-print {
                display: none;
            }
        }
    </style>
    ';
    ?>

    {!! $style !!}
</head>
<body onload="window.print()">
    <button class="btn-print" style="position: absolute; right: 1rem; top: rem;" onclick="window.print()">Print</button>
    <div class="text-center">
        <h3 style="margin-bottom: 5px;">KWITANSI</h3>
    </div>
    <br>
    <div>
        <p style="float: left;">{{ strtoupper($setting->nama_perusahaan) }}</p>
        <p style="float: right;">Tgl Angsuran : {{ date('d-m-Y') }}</p>
        {{-- <p style="float: left;">{{ date('d-m-Y') }}</p>
        <p style="float: right">{{ strtoupper(auth()->user()->name) }}</p> --}}
    </div>
    <div>
        <p style="float: left;">{{ strtoupper($setting->alamat) }}</p>
        <p style="float: right;">Faktur No : {{ tambah_nol_didepan($penjualan->id_penjualan, 10) }}</p>
    </div>
    <div>
        <p style="float: left;">TELP.</p>
        <p style="float: right;">No Pelanggan : </p>
    </div>
    <div class="clear-both" style="clear: both;"></div>
    {{-- <p>No: {{ tambah_nol_didepan($penjualan->id_penjualan, 10) }}</p> --}}
    {{-- <p class="text-center">===================================</p> --}}
    <p class="text-center">-----------------------------------</p>

    <br>
    <table width="50%" style="border: 0;float:left;">
        <tr>
            <td style="width: 25%;">Telah Terima Dari</td>
            <td style="width: 75%;">: </td>
        </tr>
        <tr>
            <td style="width: 25%;">Sejumlah Uang</td>
            <td style="width: 75%;">: {{ format_uang($penjualan->total_harga) }}</td>
        </tr>
    </table>
    <table width="100%" style="border: 1px solid;">
        <thead>
            <tr>
                <th align="left">NO</th>
                <th align="left">KETERANGAN</th>
                <th align="left">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $detailNumber = 1;
            @endphp
            @foreach ($detail as $item)
                <tr>
                    <td>{{$detailNumber}}</td>
                    <td>{{ $item->produk->nama_produk }}  {{ $item->jumlah }} x {{ format_uang($item->harga_jual) }}</td>
                    <td>{{ format_uang($item->jumlah * $item->harga_jual) }}</td>
                </tr>
                @php
                    $detailNumber += 1;
                @endphp
            @endforeach
        </tbody>
    </table>
    {{-- <table width="100%" style="border: 0;">
        @foreach ($detail as $item)
            <tr>
                <td colspan="3">{{ $item->produk->nama_produk }}</td>
            </tr>
            <tr>
                <td>{{ $item->jumlah }} x {{ format_uang($item->harga_jual) }}</td>
                <td></td>
                <td class="text-right">{{ format_uang($item->jumlah * $item->harga_jual) }}</td>
            </tr>
        @endforeach
    </table> --}}
    <p class="text-center">-----------------------------------</p>

    <table width="100%" style="border: 0;">
        <tr>
            <td>Total Harga:</td>
            <td class="text-right">{{ format_uang($penjualan->total_harga) }}</td>
        </tr>
        <tr>
            <td>Total Item:</td>
            <td class="text-right">{{ format_uang($penjualan->total_item) }}</td>
        </tr>
        <tr>
            <td>Diskon:</td>
            <td class="text-right">{{ format_uang($penjualan->diskon) }}</td>
        </tr>
        <tr>
            <td>Total Bayar:</td>
            <td class="text-right">{{ format_uang($penjualan->bayar) }}</td>
        </tr>
        <tr>
            <td>Diterima:</td>
            <td class="text-right">{{ format_uang($penjualan->diterima) }}</td>
        </tr>
        <tr>
            <td>Kembali:</td>
            <td class="text-right">{{ format_uang($penjualan->diterima - $penjualan->bayar) }}</td>
        </tr>
    </table>

    <p class="text-center">===================================</p>
    <p class="text-center">-- TERIMA KASIH --</p>

    <script>
        let body = document.body;
        let html = document.documentElement;
        let height = Math.max(
                body.scrollHeight, body.offsetHeight,
                html.clientHeight, html.scrollHeight, html.offsetHeight
            );

        document.cookie = "innerHeight=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "innerHeight="+ ((height + 50) * 0.264583);
    </script>
</body>
</html>
