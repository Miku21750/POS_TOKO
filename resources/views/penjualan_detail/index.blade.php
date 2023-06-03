@extends('layouts.master')

@section('title')
    Transaksi Penjualan
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-penjualan tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Transaksi Penjaualn</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">

                <form class="form-produk">
                    @csrf
                    <div class="form-group row">
                        <label for="kode_produk" class="col-lg-2">Kode Produk</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="id_penjualan" id="id_penjualan" value="{{ $id_penjualan }}">
                                <input type="hidden" name="id_produk" id="id_produk">
                                <input type="text" class="form-control" name="kode_produk" id="kode_produk">
                                <span class="input-group-btn">
                                    <button onclick="tampilProduk()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th width="15%">Jumlah</th>
                        <th>Diskon</th>
                        <th>Nego</th>
                        <th>Subtotal</th>
                        <th width="20%">Serial Number</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tampil-bayar bg-primary"></div>
                        <div class="tampil-terbilang"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('transaksi.simpan') }}" class="form-penjualan" method="post">
                            @csrf
                            <input type="hidden" name="id_penjualan" value="{{ $id_penjualan }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="bayar" id="bayar">
                            <input type="hidden" name="id_member" id="id_member" value="{{ $memberSelected->id_member }}">

                            <div class="form-group row">
                                <label for="dateCustom" class="col-lg-2 control-label">Tanggal</label>
                                <div class="col-lg-8">
                                    <input type="date" id="dateCustom" name="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kode_member" class="col-lg-2 control-label">Member</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="kode_member" value="{{ $memberSelected->kode_member }}">
                                        <span class="input-group-btn">
                                            <button onclick="tampilMember()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskon" class="col-lg-2 control-label">Diskon</label>
                                <div class="col-lg-8">
                                    <input type="number" name="diskon" id="diskon" class="form-control"
                                        value="{{ ! empty($memberSelected->id_member) ? $diskon : 0 }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskon" class="col-lg-2 control-label">Potongan</label>
                                <div class="col-lg-8">
                                    <input type="number" id="potongan" class="form-control" name="potongan">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ppn" class="col-lg-2 control-label">PPN?</label>
                                <input type="checkbox" id="ppn" name="ppn" class="col-2" value="1">
                                <input type="text" name="ppnrp" id="ppnrp" hidden>
                            </div>
                            <div class="form-group row">
                                <label for="bayar" class="col-lg-2 control-label">Bayar</label>
                                <div class="col-lg-8">
                                    <input type="text" id="bayarrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diterima" class="col-lg-2 control-label">Metode Pembayaran</label>
                                <div class="col-lg-8">
                                    <!-- <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $penjualan->diterima ?? 0 }}"> -->
                                    <select name="payment" id="payment" class="form-control" required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        <option value="Cash">Cash</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="Debit">DEBIT</option>
                                        <option value="BRIS">BRIS</option>
                                        <option value="transfer">transfer</option>
                                        <option value="Akulaku">Akulaku</option>
                                        <option value="Kredivo">Kredivo</option>
                                        <option value="qriscash">QRIS+cash</option>
                                        <option value="debitcash">DEBIT+cash</option>
                                        <option value="briscash">BRIS+cash</option>
                                        <option value="tfcash">Transfer+cash</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diterima" class="col-lg-2 control-label">Diterima</label>
                                <div class="col-lg-8">
                                    <input type="number" id="diterima" class="form-control" name="diterima" value="{{ $penjualan->diterima ?? 0 }}">
                                </div>
                            </div>
                            <div class="form-group row cashdiv" hidden>
                                <label for="cash" class="col-lg-2 control-label">Cash</label>
                                <div class="col-lg-8">
                                    <input type="number" id="cash" class="form-control" name="cash" value="0">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembali" id="kembaliLabel" class="col-lg-2 control-label">Kembali</label>
                                <div class="col-lg-8">
                                    <input type="number" id="kembali" name="kembali" class="form-control" value="0" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembali" id="kembaliLabel" class="col-lg-2 control-label">Keterangan</label>
                                <div class="col-lg-8">
                                    <textarea name="ket" id="ket" cols="30" rows="10" class="form-control"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

@includeIf('penjualan_detail.produk')
@includeIf('penjualan_detail.member')
@endsection

@push('scripts')
<script>
    let table, table2;

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-penjualan').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('transaksi.data', $id_penjualan) }}",
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'diskon'},
                {data: 'nego'},
                {data: 'subtotal'},
                {data: 'sn'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            var diskon = $('#diskon').val();
            var diterima = $('#diterima').val();
            var potongan = $('#potongan').val();


            loadForm(diskon, diterima, potongan);
            setTimeout(() => {
                $('#diterima').trigger('input');
            }, 300);
        });
        table2 = $('.table-produk').DataTable();

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let jumlah = parseInt($(this).val());

            // let nego = $('.nego').val();
            // return console.log(nego,jumlah)
            // let sn = $('.sn').val();
            if (jumlah < 1) {
                $(this).val(1);l
                alert('Jumlah tidak boleh kurang dari 1');
                return;
            }
            if (jumlah > 10000) {
                $(this).val(10000);
                alert('Jumlah tidak boleh lebih dari 10000');
                return;
            }


            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah,
                    // 'subtotal': subtotal,
                    // 'nego': nego,
                    // 'sn': sn
                })
                .done(response => {
                    $(this).on('focusout', function () {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });
        $(document).on('input','.sn',function(){
            let id = $(this).data('id');
            let sn = $(this).val();
            // let jumlah = $('.quantity').val();

            // let nego = $('.nego').val();
            // if (s 1) {
            //     $(this).val(1);l
            //     alert('Jumlah tidak boleh kurang dari 1');
            //     return;
            // }
            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    // 'jumlah': jumlah,
                    // 'subtotal': subtotal,
                    // 'nego': nego,
                    'sn': sn,
                })
                .done(response => {
                    $(this).on('focusout', function () {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });


        })
        $(document).on('input','.nego',function(){
            let id = $(this).data('id');
            let nego = parseInt($(this).val());

            // let jumlah = $('.quantity').val();

            // let sn = $('.sn').val();
            // if (s 1) {
            //     $(this).val(1);l
            //     alert('Jumlah tidak boleh kurang dari 1');
            //     return;
            // }
            $.post(`{{ url('/transaksi') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    // 'jumlah': jumlah,
                    // 'subtotal': subtotal,
                    'nego': nego,
                    // 'sn': sn,
                })
                .done(response => {
                    $(this).on('focusout', function () {
                        table.ajax.reload(() => loadForm($('#diskon').val()));
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });


        })

        $(document).on('input', '#diskon', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($(this).val());
        });

        $('#diterima').on('input', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($('#diskon').val(), $(this).val(), $('#potongan').val(),$('#payment').val());
        }).focus(function () {
            $(this).select();
        });
        $('#potongan').on('input', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($('#diskon').val(), $('#diterima').val(),$(this).val(),$('#payment').val());
        }).focus(function () {
            $(this).select();
        });
        $('#ppn').on('change', function(){
            loadForm($('#diskon').val(), $('#diterima').val(),$('#potongan').val(),$('#payment').val());
            if(this.checked) {
                $('#ppnrp').attr('hidden', false);
            }else{
                $('#ppnrp').attr('hidden', true);
            }
        })
        $('#payment').on('change', function(){
            if ($(this).val() == "") {
                $(this).val(0).select();
            }
            loadForm($('#diskon').val(), $('#diterima').val(),$('#potongan').val(),$(this).val());
            console.log(this)
            if(this.value === "qriscash" || this.value === "debitcash" || this.value === "briscash" || this.value == 'tfcash'){
                $('.cashdiv').attr('hidden',false)
            }else{
                $('.cashdiv').attr('hidden',true)
            }
        })
        $('#cash').on('input', function(){
            loadForm($('#diskon').val(), $('#diterima').val(),$('#potongan').val(),$('#payment').val());
        })
        $('#ket').on('input',function(){
            loadForm($('#diskon').val(), $('#diterima').val(),$('#potongan').val(),$('#payment').val());
        })

        $('.btn-simpan').on('click', function () {
            $('.form-penjualan').submit();
        });
    });
    var dateNow = formatDate(new Date());
    $('#dateCustom').val(dateNow)
    function tampilProduk() {
        $('#modal-produk').modal('show');
    }

    function hideProduk() {
        $('#modal-produk').modal('hide');
    }

    function pilihProduk(id, kode) {
        $('#id_produk').val(id);
        $('#kode_produk').val(kode);
        hideProduk();
        tambahProduk();
    }

    function tambahProduk() {
        $.post("{{ route('transaksi.store') }}", $('.form-produk').serialize())
            .done(response => {
                $('#kode_produk').focus();
                table.ajax.reload(() => loadForm($('#diskon').val()));
            })
            .fail(errors => {
                alert('Tidak dapat menyimpan data');
                return;
            });
    }

    function tampilMember() {
        $('#modal-member').modal('show');
    }

    function pilihMember(id, kode) {
        $('#id_member').val(id);
        $('#kode_member').val(kode);
        $('#diskon').val('{{ $diskon }}');
        loadForm($('#diskon').val());
        $('#diterima').val(0).focus().select();
        hideMember();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload(() => loadForm($('#diskon').val()));
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }

    function loadForm(diskon, diterima, potongan,payment) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());
        if($('#ppn').checked) {
            $('#ppnrp').attr('hidden', false);
        }else{
            $('#ppnrp').attr('hidden', true);
        }
        if($('#payment').val() === "qriscash" || $('#payment').val() === "debitcash" || $('#payment').val() === "briscash" || $('#payment').val() == 'tfcash'){
            $('.cashdiv').attr('hidden',false)
        }else{
            $('.cashdiv').attr('hidden',true)
        }

        // var yearnow = dateNow.getYear() + 1900;
        // var monthNow = dateNow.getMonth();
        // var dayNow = dateNow.getDate();
        // var dayJq = yearnow+'-'+monthNow+'-'+dayNow
        if(diskon == ""){
            diskon = 0
        }
        if(diterima == ""){
            diterima = 0
        }
        if(potongan == ""){
            potongan = 0
        }
        if(payment == undefined || payment === ""){
            payment = "cash"
        }
        var ppn = 0;
        if($('#ppn').is(':checked')){
            ppn = 1;
        }
        var cash = $('#cash').val()
        // var potongan = $('#potongan').val();
        // console.log(payment)

        var ket = $('#ket').val()

        // return var_dump($diskon);

        $.get(`{{ url('/transaksi/loadform') }}/${diskon}/${$('.total').text()}/${diterima}/${ppn}/${potongan}/${payment}/${cash}`)
            .done(response => {
                $('#totalrp').val('Rp. '+ response.totalrp);
                $('#bayarrp').val('Rp. '+ response.bayarrp);
                $('#bayar').val(response.bayar);
                $('.tampil-bayar').text('Bayar: Rp. '+ response.bayarrp);
                $('.tampil-terbilang').text(response.terbilang);
                $('#ppnrp').val(response.pajak)
                if ($('#diterima').val() != 0) {
                    if(response.kembali < 0){
                        var kembali = new Intl.NumberFormat('id-ID').format(Math.abs(response.kembali))
                        // $('#kembali').val('Rp.'+ kembali);
                        $('#kembali').val(Math.abs(response.kembali));
                        $('#kembaliLabel').text('Sisa');
                        $('.tampil-bayar').text('Sisa: Rp. '+ kembali);
                    }else{
                        $('#kembali').val(Math.abs(response.kembali));
                        $('#kembaliLabel').text('Kembali');
                        $('.tampil-bayar').text('Kembali: Rp. '+ response.kembalirp);
                    }
                    $('.tampil-terbilang').text(response.kembali_terbilang);
                }
            })
            .fail(errors => {
                alert('Tidak dapat menampilkan data');
                return;
            })
    }
    function pad2digits (num){
        return num.toString().padStart(2, '0')
    }
    function formatDate(date){
        return(
            [
                date.getFullYear(),
                pad2digits(date.getMonth()+1),
                pad2digits(date.getDate()),
            ].join('-')
            // +
            // ' '
            // +[
            //     pad2digits(date.getHours()),
            //     pad2digits(date.getMinutes()),
            //     // pad2digits(date.getSeconds()),
            // ].join(':')
        )
    }
</script>
@endpush
