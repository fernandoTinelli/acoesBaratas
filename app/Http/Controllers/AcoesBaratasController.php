<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\PlanilhaImport;

class AcoesBaratasController extends Controller
{
  public static int $TABELA_ACAO_POS_CODIGO_ACAO = 0;
  public static int $TABELA_ACAO_POS_PRECO_ACAO  = 2;
  public static int $TABELA_ACAO_POS_MARGEM_EBIT = 9;
  public static int $TABELA_ACAO_POS_EV_EBIT     = 22;
  public static int $TABELA_ACAO_POS_LIQUIDEZ    = 25;

  public static int $MIN_LIQUIDEZ_ACAO = 200000;

  private static array $ARR_ACOES_RECUSADAS = array(
    'SULA3', 'SULA4', 'SULA11', 'PSSA3', 'BOBR4', 'POSI3', 'RAPT4', 'TIET11', 'AESB3',
    'CYRE3', 'HAGA4', 'AZEV3', 'AZEV4', 'CEBR3', 'CEBR6', 'DMMO3', 'MWET4', 
  );

  private static array $ARR_ACOES_NUM_CODIGO_INVALIDOS = array (
    '33',
  );

  public function index() {
    return view('acoesBaratas.index');
  }

  public function store(Request $request) {
    setlocale(LC_MONETARY, 'pt_BR');

    $fileName = $request->input('file');

    if ($request->hasFile($fileName)) {
      $tabelaExcelAcoes = \Maatwebsite\Excel\Facades\Excel::toArray(new PlanilhaImport(), $request->file('file'));

      $prevAcao = '';
      $prevLiq  = 0;
      $bolFirst = true;
      $arrAcoes = array();

      foreach ($tabelaExcelAcoes[0] as $linhaTabela) {
        if (!is_null($linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO])) {
          
          if (!$bolFirst) {
            
            if ($linhaTabela[self::$TABELA_ACAO_POS_LIQUIDEZ] >= self::$MIN_LIQUIDEZ_ACAO && 
                  $linhaTabela[self::$TABELA_ACAO_POS_MARGEM_EBIT] >= 0 && 
                  $linhaTabela[self::$TABELA_ACAO_POS_MARGEM_EBIT] != 'NA' && 
                  !$this->isAcaoNumCodigoRecusada($linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO]) && 
                  !$this->isAcaoRecusada($linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO])) {
                    
              if (substr($prevAcao, 0, 3) == substr($linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO], 0, 3)) {
                
                if ($linhaTabela[self::$TABELA_ACAO_POS_LIQUIDEZ] > $prevLiq) {
                  
                  $arrAcoes[count($arrAcoes)-1] = array(
                    $linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO],
                    $this->formatarDin($linhaTabela[self::$TABELA_ACAO_POS_PRECO_ACAO]),
                    $linhaTabela[self::$TABELA_ACAO_POS_MARGEM_EBIT],
                    $this->formatarDin($linhaTabela[self::$TABELA_ACAO_POS_LIQUIDEZ]),
                    $linhaTabela[self::$TABELA_ACAO_POS_EV_EBIT],
                  );

                  $prevAcao = $linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO];
                  $prevLiq  = $linhaTabela[self::$TABELA_ACAO_POS_LIQUIDEZ];
                }
              } else {
                $arrAcoes[] = array(
                  $linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO], 
                  $this->formatarDin($linhaTabela[self::$TABELA_ACAO_POS_PRECO_ACAO]),
                  $linhaTabela[self::$TABELA_ACAO_POS_MARGEM_EBIT], 
                  $this->formatarDin($linhaTabela[self::$TABELA_ACAO_POS_LIQUIDEZ]), 
                  $linhaTabela[self::$TABELA_ACAO_POS_EV_EBIT],
                );

                $prevAcao = $linhaTabela[self::$TABELA_ACAO_POS_CODIGO_ACAO];
                $prevLiq  = $linhaTabela[self::$TABELA_ACAO_POS_LIQUIDEZ];
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

  private function isAcaoRecusada(string $strCodigoAcao) {
    return in_array($strCodigoAcao, self::$ARR_ACOES_RECUSADAS);
  }

  private function isAcaoNumCodigoRecusada(string $strCodigoAcao) {
    return in_array(substr($strCodigoAcao, 4, 2), self::$ARR_ACOES_NUM_CODIGO_INVALIDOS);
  }

  private function formatarDin(string $strValor) {
    return number_format($strValor, 2, ',', '.');
  }
}
