<?php

    header('Content-Type: application/json;charset=UTF-8');
    //ini_set( 'default_charset', 'iso-8859-1');
    error_reporting(E_ALL);	

    if (isset($_POST['AuthToken'])) {
        $auth_token = $_POST['AuthToken'];
     
    } else if(isset($_GET['AuthToken'])) {
         $auth_token = $_GET['AuthToken']; 
 
     } 

     if (isset($_POST['Metodo'])) {
        $metodo = $_POST['Metodo'];
     
    } else if(isset($_GET['Metodo'])) {
         $metodo = $_GET['Metodo']; 
 
     } 

    if(!$auth_token=='FlnPoU230xGF') {
        $arr_result = array('ERRO'=>2, 'ErroMsg' => 'Token invalido');
        $str_json =json_encode($arr_result);
        echo $str_json;
        exit;

    }

    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    //require_once "classes/academia_info.php";

    switch($metodo){

        case "LoginAluno":

            include "login_aluno.php";

            $celular = $_GET['AlunoCelular'];

            $arr_result = array('ALUNO_LOGIN'=>AlunoLogin($celular));

        break;


        case "LoginAlunosList":

            include "login_alunos_list.php";

            $arr_result = array('ALUNOS_LIST'=>LoginAlunosList());

        break;

        case "ProfileMain":

            include "profile_main.php";
            
            $id_aluno = $_GET['AlunoID'];

            $arr_result = array('PROFILE_MAIN'=>AlunoProfileMain($id_aluno));
            
            break;        

        case "ProfileDados":

            include "profile_dados.php";

            $id_aluno = $_GET['AlunoID'];

            $arr_result = array('PROFILE_DADOS'=>AlunoProfileDados($id_aluno));
            
            break;   

        case "ProfileVendas":

            include "profile_vendas.php";
            //include "classes/crypt_string.php";

            $id_aluno = $_GET['AlunoID'];

            $arr_result = array('PROFILE_VENDAS'=>AlunoProfileVendas($id_aluno));
            
            break;   

        case "ProfileVendaParcelas":

            include "profile_vendas_parcelas.php";
            include "classes/crypt.php";

            $id_venda = $_GET['VendaID'];

            $arr_result = array('PROFILE_VENDA_PARCELAS'=>AlunoProfileVendaParcelas($id_venda));
            
            break;               

        case "ProfileCartoes":

            include "profile_cartoes.php";
            include "classes/crypt.php";

            $id_aluno = $_GET['AlunoID'];

            $arr_result = array('PROFILE_CARTOES'=>AlunoProfileCartoes($id_aluno));
            
            break;          

        case "ContentHome":

            include "content_home.php";
            include "content_treinos.php";
            include "classes/finan_item_status.php";
            include "content_conteudos.php";
            

            $id_aluno = ($_GET['AlunoID'] ? $_GET['AlunoID'] : 0);

            $arr_result = array('HOME_CONTENT'=>AlunoContent($id_aluno));
            
            break;

        case "ContentBadges":
            include "content_badges.php";

            $id_aluno = $_GET['AlunoID'];
            $arr_result = array('BADGES'=>AlunoBadges($id_aluno));
            
            break;                
        
        case "AlunoAcessos":
            include "classes/finan_item_status.php";

            $id_aluno = $_GET['AlunoID'];
            $aula_tipo = $_GET['AulaTipo'];      //'aula_live' / 'treinos_guiados'
            $arr_result = array('ALUNO_ACESSOS' => AlunoAcessos($id_aluno, $aula_tipo));            
            
            break;

        case "TreinosCategorias":

            include "content_treinos.php";

            $categoria_has_treinos = ($_GET['CategoriaHasTreinos'] ? $_GET['CategoriaHasTreinos'] : False);

            $arr_result = array('TREINOS_CATEGORIAS'=>ContentTreinosCategorias($categoria_has_treinos));
            
            break;              

        case "TreinosList":

            include "content_treinos.php";
            include "classes/finan_item_status.php";

            $id_aluno = $_GET['AlunoID'];
            $treino_categoria_id = $_GET['CategoriaID'] ?: NULL;
            $treino_id = $_GET['TreinoID'] ?: NULL;

            $arr_result = array('TREINOS_LIST' => ContentTreinosList($id_aluno, $treino_categoria_id, $treino_id));

            break;    

        case "TreinoExercicios":

            include "content_treinos.php";

            $treino_id = $_GET['TreinoID'];

            $arr_result = array('TREINO_EXERCICIOS'=>ContentTreinoExercicios($treino_id));
            
            break;          
            
            
        case "LiveHorarios":

            include "content_lives.php";
            include "classes/finan_item_status.php";

            //$turma_id = ($_GET['TurmaID'] ? $_GET['TurmaID'] : NULL);
            $id_aluno = $_GET['AlunoID'] ?: NULL;
            $turma_id = $_GET['TurmaID'] ?: NULL;

            $arr_result = array('LIVE_HORARIOS'=>ContentLivesHorarios($id_aluno, $turma_id));
            
            break;   


        case "ConteudosList":

            include "content_conteudos.php";
            include "classes/finan_item_status.php";
            
            $conteudo_id = ($_GET['ConteudoID'] ? $_GET['ConteudoID'] : NULL);
            $conteudo_tipo = ($_GET['ConteudoTipo'] ? $_GET['ConteudoTipo'] : NULL);
            //conteudo_aluno: conteúdo comprado pelo aluno
            //conteudo_pago: conteúdo pago (ainda não comprado pelo aluno)
            //conteudo_free

            $id_aluno = $_GET['AlunoID'];


            $arr_result = array('CONTEUDOS_LIST'=>ContentConteudosList($id_aluno, $conteudo_tipo, $conteudo_id));
            
            break;
  
            
        case "ConteudoEpisodios":

            include "content_conteudos.php";

            $conteudo_id = $_GET['ConteudoID'];

            $arr_result = array('CONTEUDO_EPISODIOS'=>ContentConteudoEpisodios($conteudo_id));
            
            break;              

        case "ConteudoEpisodioSessoes":

            include "content_conteudos.php";

            $episodio_id = $_GET['EpisodioID'];

            $arr_result = array('CONTEUDO_EPISODIO_SESSOES'=>ContentConteudoEpisodioSessoes($episodio_id));
            
            break;               

        case "StorePlanos":

            include "store_planos_list.php";

            $id_plano = ($_GET['PlanoID'] ?: NULL);
            $id_vigencia = ($_GET['VigenciaID'] ?: NULL);
            //$perfis = ($_GET['Perfis'] ? $_GET['Perfis'] : NULL);
            //$vigencia = ($_GET['Vigencia'] ? $_GET['Vigencia'] : NULL);

            $arr_result = array('PLANOS_LIST' => PlanosList($id_plano, $id_vigencia));
            
            break; 
        
        case "PresencaRegistro":
            include "presenca_registro.php";
            $ret = PresencaRegistro($_GET);
            $arr_result = array('PRESENCA_REGISTRO' => $ret);
            http_response_code(intval($$ret['status']));
            break;

    }   

    if ($no_json) {
        //echo $arr_result;
        print_r($arr_result);
        
    } else {

        
        //print_r($arr_result);
    
        $dados = convert_to_utf8_recursively($arr_result);
        //$str_json = json_encode($dados, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
        $str_json = json_encode($dados, JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);

        echo $str_json;
    }

    /*
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo 'No errors';
            break;
        case JSON_ERROR_DEPTH:
            echo 'Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo 'Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo 'Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            echo 'Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            echo 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            echo 'Unknown error';
            break;
    }    

*/
?>
