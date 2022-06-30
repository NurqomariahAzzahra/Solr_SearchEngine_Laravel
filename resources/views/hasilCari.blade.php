@extends('searchAwal')
@section('content')
<div id="result">
    <div class="container">
        <!-- tampilkan nilai numFound-->
        <div class="row">
            <div class="col-md-12">
                <h3 style="color: black">Hasil Pencarian</h3>
                <p style="color: black">Ditemukan {{$jumlahData}} <span id="numFound"></span> hasil pencarian untuk kata kunci <span id="keyword"></span></p>
            </div>
        </div>
        @foreach ($hasil1 as $h)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><a href="{{ $h['id'] }}" class="text-start max-desc">{{ $h['id'] }}</a></h5>
                <h6 class="card-subtitle mb-2 text-muted">{{ $h ['title_txt_id'] }}</h6>
                <p class="card-text max-body" style="color:black">{{ $h['body_txt_id']}}</p>
            </div>
        </div>
        <br>
        @endforeach
        <!-- Tampilkan paginate dengan bootstrap -->
        <center>
            @if ($banyakHalaman > 1)
            @for ($i = 1; $i <= $banyakHalaman; $i++) <!-- style boostrap pagination -->
                <a href="/search?halaman={{$i}}&search={{$prevSearch}}" class="btn btn-danger">{{$i}}</a>
                @endfor
                @endif
        </center>
    </div>
</div>
@endsection