<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PlanilhaImport;

class AcoesBaratasController extends Controller
{
    public function index() {
        return view('acoesBaratas.index');
    }

    public function store(Request $request) {
        setlocale(LC_MONETARY, 'pt_BR');

        $arrAcoesRecusadas = array(
            'SULA3', 'SULA4', 'SULA11', 'PSSA3', 'BOBR4', 'POSI3', 'RAPT4', 'TIET11', 'AESB3',
            'CYRE3', 'HAGA4',
        );

        $fileName = $request->input('file');

        if ($request->hasFile($fileName)) {
            $table = \Maatwebsite\Excel\Facades\Excel::toArray(new PlanilhaImport(), $request->file('file'));

            $prevAcao = '';
            $prevLiq = 0;
            $bolFirst = true;
            $arrAcoes = array();
            foreach ($table[0] as $row) {
                if (!is_null($row[0])) {
                    if (!$bolFirst) {
                        if ($row[25] >= 200000 && $row[9] >= 0 && $row[9] != 'NA' && intval(substr($row[0], 4, 2)) != 33 && !in_array($row[0], $arrAcoesRecusadas)) {
                            if (substr($prevAcao, 0, 3)  == substr($row[0], 0, 3)) {
                                if ($row[25] > $prevLiq) {
                                    $arrAcoes[count($arrAcoes)-1] = array(
                                        $row[0] , number_format($row[2], 2, ',', '.'), $row[9], number_format($row[25], 2, ',', '.'), $row[22],
                                    );
                                    $prevAcao = $row[0];
                                    $prevLiq = $row[25];
                                }
                            } else {
                                $arrAcoes[] = array(
                                    $row[0] , number_format($row[2], 2, ',', '.'), $row[9], number_format($row[25], 2, ',', '.'), $row[22],
                                );
                                $prevAcao = $row[0];
                                $prevLiq = $row[25];
                            }                            
                        }
                    } else {
                        $bolFirst = false;
                    }
                }
            }

            usort($arrAcoes, array("App\Http\Controllers\AcoesBaratasController", "sortArrAcoes"));

            return view('acoesBaratas.lista', compact('arrAcoes'));
        } else {
            return array();
        }
    }

    static private function sortArrAcoes(array $arr1, array $arr2) {
        if ($arr1[4] == $arr2[4]) {
             return 0;
        }

        return ($arr1[4] < $arr2[4]) ? -1 : 1;
    }
}
