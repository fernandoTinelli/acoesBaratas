@extends('template.core')

@section('title', 'Listar')

@section('main')
  <main class="container">
    <div class="row">
      <label for="nbtListar" class="col-sm-2 col-md-1 col-12 col-form-label fw-bold">Visualizar: </label>
      <div class="offset-sm-1 col-sm-2 col-md-2 col-12">
        <input type="number" id="nbrListar" value="10" min="10" max="20" class="form-control">
      </div>
      <label for="txtPesquisar" class="col-sm-3 offset-md-2 col-md-2 col-12 col-form-label fw-bold">Pesquisar: </label>
      <div class="wrapper col-sm-4 col-md-4 col-12">
        <div class="search-input">
          <a href="" target="_blank" hidden></a>
          <input id="txtPesquisar" type="text" placeholder="VALE3" class="form-control">
          <div class="autocom-box"></div>
          <div class="icon"><i class="fas fa-search"></i></div>
        </div>
      </div>
    </div>

    <hr>

    <div>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Codigo</th>
            <th scope="col">Preço</th>
            <th scope="col">Margem Ebit</th>
            <th scope="col">Liquidez</th>
            <th scope="col">Ev/Ebit</th>
            <th scope="col">Açoes</th>
          </tr>
        </thead>
        <tbody id="tbody">
        </tbody>
      </table>
      <div id="btnLimparMarcacoes" class="row" style="display: none;">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
          <button class="btn btn-link" type="button">Limpar Marcacoes</button>
        </div>
      </div>
      <div class="row">
        <nav>
          <ul id="pg" class="pagination justify-content-center">
          </ul>
        </nav>
      </div>  
    </div>
  </main>
@endsection

@section('bodyScripts')
<script>
  let arrAcoes         = new Array();
  let arrBusca         = new Array();
  let arrBuscaIndexada = new Array();
  let tam              = 0;
  let pagina;
  let maxPagina;  

  @foreach ($arrAcoes as $i => $acao)
    strAcao = '{{ json_encode(str_replace(",", ".", $acao)) }}';
    strAcao = strAcao
      .replace(/&quot;/g, '')
      .replace('[', '')
      .replace(']', '');
    arrAcoes[tam] = strAcao.split(',');
    arrBusca[tam] = arrAcoes[tam][0];
    arrBuscaIndexada[arrAcoes[tam][0]] = tam;
    tam++;
  @endforeach
</script>

<script type="text/javascript" src="{{ url('js/lista.js') }}"></script>
@endsection