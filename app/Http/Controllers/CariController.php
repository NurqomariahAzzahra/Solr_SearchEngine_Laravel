<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CariController extends Controller
{
    //
    public function search(Request $request)
    {
        //Untuk menghitung paginate
        function hitungPaginate($banyakData, $perPage = 5)
        {
            return (int)(ceil($banyakData / $perPage));
        }

        // contoh setelah dimasukin query ke solr
        //fungsi untuk mengubah spasi menjadi %20 
        // agar bisa dimasukkan ke query
        function convertSearchKey($key)
        {
            $key = (trim($key));
            $searchKey = '';
            if (Str::contains($key, ' ')) {
                $result = explode(' ', $key);
                foreach ($result as $res) {
                    $searchKey .= $res . "%20";
                }
                $searchKey = substr($searchKey, 0, -3);
                return $searchKey;
            }

            return $key;
        }

        function paginate($items, $perPage = 5, $page = 1)
        {
            // deklarasi variable item yang akan ditampilkan
            // sementara akan diisi seluruh item
            // kemudian nanti akan direplace dengan array_slice
            $itemToShow = $items;
            $currentPage = $page;
            //cek jika pagenya bukan page pertama
            //set posisi startnya
            if ($currentPage > 1) {
                $start = ($currentPage * $perPage) - $perPage;
            } else {
                $start = 0;
            }

            $itemToShow = array_slice($items, $start, $perPage);

            return $itemToShow;
        }

        // menggabungkan input user dengan url pada apache solr
        //http://192.168.99.100:8983/solr/tugas/select?indent=true&q.op=OR&q=title_txt_id%3ALagu&rows=800&start=0
        $url = "http://localhost:8983/solr/core_uas/select?indent=true&q.op=OR&q=title_txt_id%3A" . convertSearchKey($request->search) . "&rows=800&start=0";

        // $url = "http://192.168.99.100:8983/solr/core_uas/select?indent=true&q.op=OR&q=title_txt_id%3A" . convertSearchKey($request->search) . "&rows=800&start=0";

        // get kontennya
        $result = file_get_contents($url);

        // decode jsonnya jadi array paginate
        $hasil = json_decode($result, true);

        $jumlahData = $hasil["response"]["numFound"];
        $hasil1 = $hasil['response']['docs'];
        $banyakHalaman = 1;
        // set hasil yang akan tampil hanya 5
        $perPage = 5;
        // cek jika jumlah ditemukan pada solr > 5
        if ($hasil['response']['numFound'] > 5) {

            // hitung banyak halaman yang akan ditampilkan dari data yang ditemukan
            $banyakHalaman = hitungPaginate($hasil['response']['numFound'], $perPage);
            // paginate halaman tersebut
            $hasil1 = paginate($hasil1, $perPage, $request->halaman);
        }

        $previousSearch = convertSearchKey($request->search);

        return view('hasilCari', [
            'jumlahData' => $jumlahData,
            'hasil1' => $hasil1,
            'banyakHalaman' => $banyakHalaman,
            'prevSearch' => $previousSearch,
        ]);
    }
}
