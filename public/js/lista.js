const bolIsSessioStorageDisponivel = isLocalStorageDisponivel();
const searchWrapper = document.querySelector(".search-input");
const suggBox       = searchWrapper.querySelector(".autocom-box");
const numMinListar  = 10;
const numMaxListar  = 20;

/* Inicializa a pagina */
window.onload = () => {
  pagina = 1;
  maxPagina = Math.ceil(tam / nbrListar.value);

  criaTabela(pagina);

  /* Muda a visualizaçao */
  nbrListar.onchange = () => {
    if (nbrListar.value > numMaxListar) {
      nbrListar.value = numMaxListar;
    } else if (nbrListar.value < numMinListar) {
      nbrListar.value = numMinListar;
    }

    pagina = 1;
    maxPagina = Math.ceil(tam / nbrListar.value);

    criaTabela(pagina);
  }

  btnLimparMarcacoes.onclick = () => {
    if (bolIsSessioStorageDisponivel) {
      localStorage.clear();
      criaTabela(pagina);
    }
  }
};

/* Busca por uma acao */
txtPesquisar.onkeyup = (e) => {
  let userData   = e.target.value;
  let emptyArray = [];
  
  if (userData) {
    emptyArray = arrBusca.filter((data) => data.toLocaleLowerCase().startsWith(userData.toLocaleLowerCase()));
    emptyArray = emptyArray.map((data) => data = '<li>'+ data +'</li>');

    searchWrapper.classList.add("active");
    mostrarSugestoes(emptyArray);
    
    let allList = suggBox.querySelectorAll("li");
    for (let i = 0; i < allList.length; i++) {
      allList[i].setAttribute("onclick", "selecionar(this)");
    }
  } else {
    searchWrapper.classList.remove("active");
  }
}

function criaTabela(novaPagina) {
  if (novaPagina < 1 || novaPagina > maxPagina) {
    return;
  }

  pagina = novaPagina;

  // Limpa a Tabela
  while (tbody.lastChild) {
    tbody.removeChild(tbody.lastChild);
  }
  while (pg.lastChild) {
    pg.removeChild(pg.lastChild);
  }

  for (let i = (pagina - 1) * + nbrListar.value; i < arrAcoes.length && i < (pagina-1) * + nbrListar.value + +nbrListar.value; i++) {
    if (bolIsSessioStorageDisponivel) {
      tbody.innerHTML += `
        <tr id="tr${i}">
          <th scope="row">${i+1}</th>
          <td>${arrAcoes[i][0]}</td> 
          <td>R$ ${arrAcoes[i][1].replaceAt(arrAcoes[i][1].length-3,',')}</td>
          <td>${arrAcoes[i][2]}</td>
          <td>R$ ${arrAcoes[i][3].replaceAt(arrAcoes[i][3].length-3,',')}</td>
          <td>${arrAcoes[i][4]}</td>
          <td>
            <button id="btnTr${i}" class="btn btn-info btn-sm" title="Marcar" onclick="marcarAcao('tr${i}', 'btnTr${i}', '${arrAcoes[i][0]}')">
              <i class="bi bi-bookmark-plus"></i>
            </button>
            <button class="btn btn-danger btn-sm" title="Remover" onclick="removerAcao(tr${i})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      `;

      if (localStorage[arrAcoes[i][0]] !== null && localStorage[arrAcoes[i][0]] == 1) {
        colocarMarcaAcao('tr' + i, 'btnTr' + i, localStorage[arrAcoes[i][0]]);
      }
    } else {
      tbody.innerHTML += `
        <tr id="tr${i}">
          <th scope="row">${i+1}</th>
          <td>${arrAcoes[i][0]}</td> 
          <td>R$ ${arrAcoes[i][1].replaceAt(arrAcoes[i][1].length-3,',')}</td>
          <td>${arrAcoes[i][2]}</td>
          <td>R$ ${arrAcoes[i][3].replaceAt(arrAcoes[i][3].length-3,',')}</td>
          <td>${arrAcoes[i][4]}</td>
          <td>
            <button class="btn btn-danger btn-sm" title="Remover" onclick="removerAcao(tr${i})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
      `;
    }
  }

  pg.innerHTML = `
    <li class="page-item">
      <a class="page-link" href="#" aria-label="Anterior" onclick="criaTabela(pagina-1)">
        <span aria-hidden="true"><i class="bi bi-caret-left"></i></span>
      </a>
    </li>
  `;

  for (let i = 1; i <= maxPagina; i++) {
    pg.innerHTML += `
      <li class="page-item"><a id="pg${i}" class="page-link" href="#" onclick="criaTabela(${i})">${i}</a></li>
    `;
  }

  pg.innerHTML += `
    <li class="page-item">
      <a class="page-link" href="#" aria-label="Proximo" onclick="criaTabela(pagina+1)">
        <span aria-hidden="true"><i class="bi bi-caret-right"></i></span>
      </a>
    </li>
  `;

  document.getElementById('pg'+pagina).style.backgroundColor = 'lightblue';
  if (bolIsSessioStorageDisponivel) {
    btnLimparMarcacoes.style.display = '';
  }
}

function removerAcao(element) {
  let posicaoAcao = +(element.getAttribute('id').substring(2));
  let nomeAcao    = arrBusca[posicaoAcao];
  
  arrAcoesTemp = new Array();
  for (let i = 0; i < posicaoAcao; i++) {
    arrAcoesTemp[i] = arrAcoes[i];
  }
  for (let i = posicaoAcao + 1, j = posicaoAcao; i < arrAcoes.length; i++, j++) {
    arrAcoesTemp[j] = arrAcoes[i];
  }

  arrAcoes = arrAcoesTemp;

  arrBusca = new Array();
  arrBuscaIndexada = new Array();

  tam = 0;
  for (let i = 0; i < arrAcoes.length; i++) {
    arrBusca[tam] = arrAcoes[tam][0];
    arrBuscaIndexada[arrAcoes[tam][0]] = tam;
    tam++;
  }

  maxPagina = Math.ceil(tam / nbrListar.value);
  criaTabela(pagina);
  
  if (bolIsSessioStorageDisponivel && localStorage[nomeAcao] !== null) {
    localStorage.removeItem(nomeAcao);
  }
}

function colocarMarcaAcao(strElement, strBtnTr, numMarca) {
  let element = document.getElementById(strElement);
  let btnTr = document.getElementById(strBtnTr);

  if (numMarca == 1) {
    element.style.backgroundColor = 'lightblue';
    btnTr.innerHTML = `<i class="bi bi-bookmark-dash"></i>`;
  } else {
    element.style.backgroundColor = 'white';
    btnTr.innerHTML = `<i class="bi bi-bookmark-plus"></i>`;
  }
}

function marcarAcao(strElement, strBtnTr, strAcao) {
  let element = document.getElementById(strElement);
  let btnTr = document.getElementById(strBtnTr);

  if (bolIsSessioStorageDisponivel) {
    if (localStorage.getItem(strAcao) == null || localStorage.getItem(strAcao) == 0) {
      element.style.backgroundColor = 'lightblue';
      btnTr.innerHTML = `<i class="bi bi-bookmark-dash"></i>`;
      localStorage.setItem(strAcao, 1);
    } else {
      element.style.backgroundColor = 'white';
      btnTr.innerHTML = `<i class="bi bi-bookmark-plus"></i>`;
      localStorage.setItem(strAcao, 0);
    }
  }
}

function selecionar(element) {
  let selectData = element.textContent.trim();
  txtPesquisar.value = selectData;
  
  searchWrapper.classList.remove("active");

  if (arrBuscaIndexada[selectData] !== undefined) {
    novaPagina = Math.floor(arrBuscaIndexada[selectData] / nbrListar.value) + 1;
    criaTabela(novaPagina);
    document.getElementById('tr' + arrBuscaIndexada[selectData]).style.backgroundColor = 'lightcoral';
    txtPesquisar.value = '';
  }
}

function mostrarSugestoes(list) {
  let listData;
  if (!list.length) {
    userValue = txtPesquisar.value;
    listData = '';
  } else {
    listData = list.join('');
  }
  
  suggBox.innerHTML = listData;
}

String.prototype.replaceAt = function(index, replacement) {
  if (index >= this.length) {
    return this.valueOf();
  }
  var chars = this.split('');
  chars[index] = replacement;
  return chars.join('');
}

function isLocalStorageDisponivel() {
  try {
    localStorage.setItem('__storage_test__', '__storage_test__');
    localStorage.removeItem('__storage_test__');
    return true;
  } catch (e) {
    return false;
  }
}
