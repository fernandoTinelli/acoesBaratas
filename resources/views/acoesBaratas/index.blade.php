
@extends('template.core')

@section('title', 'Carregar')

@section('headStyles')
<style>
  form button {
    margin-top: 1em;
  }

  .modal-header {
    background-color: lightsalmon !important;
  }
</style>
@endsection

@section('main')
    <main class="container">
      <form id="form" method="POST" action="/acoesBaratas" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label for="file">Selecione a planilha de acoes:</label>    
          <input type="file" id="file" name="file" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
      </form>     

      <div class="modal" tabindex="-1" id="modal">
        <div class="modal-dialog modal-dialog-centered" >
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Aviso</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Selecione a planilha de dados.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ok</button>
            </div>
          </div>
        </div>
      </div>
    </main>
@endsection

@section('bodyScripts')
<script>
  window.onload = () => {
    form.onsubmit = () => {
      if (document.querySelector('#file').value === '') {
        let modal = new bootstrap.Modal(document.getElementById('modal'), {});
        modal.show();

        return false;
      }

      return true;
    }
  };
</script>
@endsection