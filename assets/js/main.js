const isNotLoading = `far fa-question-circle`;
const isNotOk = `far fa-times-circle red`;
const isOk = `far fa-check-circle verde`;
const isLoading = `far spinner`;
const URL = "http://localhost/obras5/codigo_fonte/vaobras-0.4.0/api/testes.php";

const closeModal = document.querySelector("#close-modal");
const popUpBtnYes = document.querySelector("#sim");
const popUpBtnNo = document.querySelector("#nao");
const popUp = document.querySelector(".pop-up");

const testBtn = document.querySelector("#testarVariaveisBtn");
const clearBtn = document.querySelector("#limparBtn");
const result = document.querySelector("#resultado");

const loaders = {
  permissao: document.querySelector("#permissaoLoader"),
  arquivos: document.querySelector("#arquivosLoader"),
  modulos: document.querySelector("#modulosLoader"),
  conexao: document.querySelector("#conexaoLoader"),
  relatorios: document.querySelector("#relatoriosLoader"),
}

const verde = "#26A65B";
const vermelho = "#CD0000";
const azul = "#4183D7";

let counter = 0;

function ativarModal() {
  popUp.style.opacity = 1;
  popUp.style.transform = "translate(-50%, -50%)";
}

function fecharModal() {
  popUp.style.opacity = 0;
  popUp.style.transform = "translate(-50%, -2000px)";
}

const modulos = [
  { func: "testarPermissoes", loader: "permissao" },
  { func: "testarBanco", loader: "conexao" },
  { func: "testarModulos", loader: "modulos" },
  { func: "testarQuantidadeArquivos", loader: "arquivos" },
  { func: "testarRelatorios", loader: "relatorios" },
]

function testarDados() {
  testBtn.innerHTML = "<span class='far spinner'></span>";
  testBtn.setAttribute("disabled", "disabled");
  for(var i in loaders) { loaders[i].className = isLoading }
  modulos.forEach(modulo => testarModulo(URL, modulo.func, modulo.loader));
  result.innerHTML +=
  `
  <hr>
    <span class="data">Horário de verificação: ${new Date().getHours()}:${new Date().getMinutes()}:${new Date().getSeconds()}</span>
  <hr>
  `;
}

function testarModulo(url, obj, nome) {
  $.post(url, { function: obj }, function(res) {
    if(res) counter++;
    result.innerHTML += `<span>${res}</span>`;
    res.toLowerCase().includes("erro") || res === "" ? loaders[nome].className = isNotOk : loaders[nome].className = isOk;
  }).then(res => {
    if(counter === Object.keys(loaders).length) {
      counter = 0;
      mudarBackground(loaders);
      testBtn.innerHTML = "CHECAR AMBIENTE";
      testBtn.removeAttribute("disabled");
    };
  });
}

function scroll(elem) {
  elem.scrollTop = elem.scrollHeight + elem.clientHeight;
}

testBtn.addEventListener("click", testarDados);
clearBtn.addEventListener("click", function() {
  document.documentElement.style.setProperty("--cor", azul);
  result.innerHTML = "";
  for(var i in loaders) { loaders[i].className = isNotLoading }
});

result.addEventListener("DOMSubtreeModified", function() { scroll(result) });

function mudarBackground(loaders) {
  let areOkLoaders = 0;
  let areNotOkLoaders = 0;
  for(var i in loaders) {
    loaders[i].className === isNotOk ? areNotOkLoaders++ : null;
    loaders[i].className === isOk ? areOkLoaders++ : null;
  }

  if(areOkLoaders === Object.keys(loaders).length) document.documentElement.style.setProperty("--cor", verde);
  if(areNotOkLoaders !== 0) document.documentElement.style.setProperty("--cor", vermelho);
}
