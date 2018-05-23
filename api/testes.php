<?php
  require_once "../../../config/config.php";
  require_once "../../libs/phpjasper-master/src/PHPJasper.php";

  use PHPJasper\PHPJasper;

  // Variáveis do banco de dados
  $host = CONF_BD_SERVER;
  $usuario = CONF_BD_USER;
  $senha = CONF_BD_PASSWD;
  $banco = CONF_BD_DATABASE;

  // Funções auxiliares
  function testarBanco() {
    global $host, $usuario, $senha, $banco;

    $str  = "host=" . $host;
    $str .= " port=5432";
    $str .= " dbname=" . $banco;
    $str .= " user=" . $usuario;
    $str .= " password=" . $senha;
    $conexao = pg_connect($str);

    if($conexao) {
      echo 'Conexão com o banco de dados funcionando normalmente. <br>';
    } else {
      echo "<strong style='color: #cd0000;''><i>ERRO:</i></strong> <strong>Não foi possível conectar ao banco de dados</strong>. <br>";
    }

    $q1 = "SELECT 'Contratos'
     as TipoDocumento, count(id_obra)
     as qtd
     FROM contrato
     WHERE caminho_contrato
     IS NOT NULL";
     $q2 = "SELECT 'Imagens'
     as TipoDocumento, count(id_obra)
     as qtd
     FROM foto
     WHERE caminho
     IS NOT NULL";

     queryAndShowData($conexao, $q1, "Documentos");
     queryAndShowData($conexao, $q2, "Imagens");

    return pg_close($conexao);
  }

  function queryAndShowData($con, $q, $nome) {
    $query = pg_query(
      $con,
      $q
    );

    if($query) {
      $n = 0;

      while($cons = pg_fetch_assoc($query)) {
        $n =  $cons['qtd'];
      }

      echo "A pasta $nome deve conter <strong>$n</strong> arquivos. <br>";
    } else { echo "<strong style=\"color: #cd0000;\"><i>ERRO:</i></strong> Falha ao realizar a query na tabela $nome. <br>"; }
  }

  function testarPermissoes() {
    $tmp = is_writable("../../relatorio/tmp");
    $templates = is_writable("../../templates");
    $uploads = is_writable("../../uploads");

    permissions($tmp, "tmp");
    permissions($templates, "templates");
    permissions($uploads, "uploads");
  }

  function permissions($dir, $nome) {
    if($dir) {
      echo "Permissão para a pasta <strong>\"$nome\"</strong> concedida e funcionando normalmente. <br>";
    } else {
      echo "<strong style='color: #cd0000;''><i>ERRO:</i></strong> A pasta <strong>\"$nome\"</strong> não contém permissões de escrita. <br>";
    }
  }

  function testarModulos() {
    gerarModulos(PHP_MODULES);
  }

  function testarQuantidadeArquivos() {
    files("../../uploads/imagens", "uploads/imagens");
    files("../../uploads/documentos", "upload/documentos");
  }

  function files($dir, $nome) {
    $c = 0;
    $n = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
    foreach ($n as $key => $value) {
      $c++;
    }

    echo "A pasta <strong>\"$nome\"</strong> contém $c arquivos. <br>";
  }

  function gerarModulos($modulos) {
    foreach ($modulos as $key) {
      if(extension_loaded($key)) {
        echo "O módulo <strong>\"$key\"</strong> está habilitado. <br>";
      } else {
        echo "<strong style=\"color: #cd0000;\"><i>ERRO:</i></strong> O módulo <strong>\"$key\"</strong> não está habilitado. <br>";
      }
    }
  }

  function testarRelatorios() {
    global $host, $usuario, $senha, $banco;
    $nome_pdf = md5(gerarnumeros());
    exec("/var/www/html/obras4/codigo_fonte/relatorio/../libs/phpjasper-master/src/../bin/jasperstarter/bin/jasperstarter process '/var/www/html/obras4/codigo_fonte/relatorio/jasper/obras_por_orgao.jasper' -o '/var/www/html/obras4/codigo_fonte/testes_ambiente/api/$nome_pdf' -f pdf -P municipio='0' situacao='0' ano_exercicio='2016' orgao='54' -t postgres -u $usuario -p $senha -H $host -n $banco --db-port 5432
");

    if(file_exists($nome_pdf . ".pdf")) {
      echo "O gerador de relatórios está funcionando normalmente. <br>";
      unlink($nome_pdf . ".pdf");
    } else {
      echo "<strong style=\"color: #cd0000;\"><i>ERRO:</i></strong> O gerador de relatórios não conseguiu gerar um pdf. <br>";
    }
  }

  function gerarnumeros($length = 10) {
     return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopkrstuvxz', ceil($length/strlen($x)) )),1,$length);
  }

  call_user_func($_POST['function']);
?>
