<?php 
    //include "classes/finan_item_status.php";

    function ContentConteudosList($id_aluno, $conteudo_tipo, $id_conteudo = NULL) {

        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'Conteudos'=> '', 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {

            $arr_conteudos = array();

            //filtra por tipo de conteúdo (conteudo_aluno, conteudo_pago, conteudo_free)
            if ($conteudo_tipo)  {

                //lista com os ids de conteúdos comprados pelo aluno
                $conteudo_aluno_list = ContentConteudosAluno($id_aluno);

                if ($conteudo_tipo == 'conteudo_aluno' && !$conteudo_aluno_list) goto NO_RESULT;
                
                //lista com os ids de conteúdos pagos ou free

                $conteudo_tipo_list = ContentConteudosTipo($conteudo_tipo, $conteudo_aluno_list);
                if (($conteudo_tipo == 'conteudo_pago' || $conteudo_tipo == 'conteudo_free') && !$conteudo_tipo_list) goto NO_RESULT;
//echo 'Conteudo Tipo: ' . $conteudo_tipo . 'Conteudo Aluno: ' . $conteudo_aluno_list . ' - Conteudo Tipo: ' . $conteudo_tipo_list;

            }


            $str_conteudos = " SELECT
            lo_produtos.lo_id_produto AS ProdutoID,
            lo_conteudos.lo_id_conteudo AS ConteudoID, 
            lo_conteudos.lo_conteudo_ordem AS ConteudoOrdem,
            lo_conteudos.lo_conteudo_nome AS ConteudoNome, 
            lo_conteudos.lo_conteudo_descricao AS ConteudoDescricao,
            (CASE WHEN lo_conteudos.lo_conteudo_imagem IS NOT NULL 
                THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_conteudos.lo_conteudo_imagem) 
                ELSE NULL 
            END) AS ConteudoImagem  
            FROM
            lo_conteudos
            INNER JOIN lo_produtos ON lo_produtos.lo_id_conteudo = lo_conteudos.lo_id_conteudo
            WHERE
            lo_conteudos.lo_conteudo_ativo = 1 
            AND lo_conteudos.lo_conteudo_data_exclusao IS NULL";

            if ($id_conteudo) {
                $str_conteudos.= " AND lo_conteudos.lo_id_conteudo = " . $id_conteudo;

            } else if ($conteudo_tipo == 'conteudo_aluno') {
                $str_conteudos.= " AND lo_conteudos.lo_id_conteudo IN (" . $conteudo_aluno_list . ")"; 

            } else {
                $str_conteudos.= " AND lo_conteudos.lo_id_conteudo IN (" . $conteudo_tipo_list . ")"; 

            }

            $str_conteudos.= " ORDER BY lo_conteudos.lo_conteudo_ordem DESC"; 

            $rs_conteudos = mysqli_query($conn, $str_conteudos);	   
            $num_conteudos = mysqli_num_rows($rs_conteudos);    
//echo $str_conteudos;
            if ($num_conteudos > 0){
                while($r = mysqli_fetch_assoc($rs_conteudos)) {

                    $conteudo_acesso = ConteudoFinanStatus($id_aluno,$r['ProdutoID']);

                    $arr_conteudos[] = array('ProdutoID'=>$r['ProdutoID'],
                                            'ConteudoID'=>$r['ConteudoID'],
                                            'ConteudoOrdem'=>$r['ConteudoOrdem'],
                                            'ConteudoNome'=>$r['ConteudoNome'],
                                            'ConteudoDescricao'=>$r['ConteudoDescricao'],
                                            'ConteudoImagem'=>$r['ConteudoImagem'],
                                            'ConteudoStatus'=>$conteudo_acesso);
                } 

                $arr_result = array('Registros'=> $num_conteudos,'Conteudos'=>$arr_conteudos,'ErroMsg'=>false); //, 'Sql'=>$str_conteudos);
                goto RESULT;

            } else {
                goto NO_RESULT;

            }

NO_RESULT:            
            $arr_result = array('Registros' => 0, 'Conteudos'=> $arr_conteudos, 'ErroMsg'=>'Nenhum conteudo encontrado'); //,'Sql'=>$str_conteudos);

RESULT:
            return $arr_result;
        }
    }

    function ContentConteudoEpisodios($id_conteudo) {

        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'ConteudoEpisodios'=> '', 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {
            $arr_episodios = array();

            $str_episodios = " SELECT
            lo_conteudo_episodios.lo_id_conteudo_episodio AS EpisodioID, 
            lo_conteudo_episodios.lo_id_conteudo AS ConteudoID, 
            lo_conteudo_episodios.lo_episodio_ordem AS EpisodioOrdem, 
            lo_conteudo_episodios.lo_episodio_titulo AS EpisodioTitulo,

            (SELECT COUNT(*) 
            FROM 
            lo_conteudo_episodio_secoes
            WHERE
            lo_conteudo_episodio_secoes.lo_id_conteudo_episodio = lo_conteudo_episodios.lo_id_conteudo_episodio 
            ) AS EpisodioSessoes

            FROM
            lo_conteudo_episodios
            WHERE
            lo_conteudo_episodios.lo_id_conteudo = " . $id_conteudo . "
            AND lo_conteudo_episodios.lo_episodio_ativo = 1
            ORDER BY
            lo_conteudo_episodios.lo_episodio_ordem";

            $rs_episodios = mysqli_query($conn, $str_episodios);	   
            $num_episodios = mysqli_num_rows($rs_episodios);    

            if ($num_episodios > 0){
          
                while($r = mysqli_fetch_assoc($rs_episodios)) {
                    $arr_episodios[] = $r;

                } 

                $arr_result = array('Registros'=> $num_episodios,
                                    'ConteudoEpisodios'=>$arr_episodios,'ErroMsg'=>false); //, 'Sql'=>$str_conteudos);

            } else {
                $arr_result = array('Registros' => 0, 'ConteudoEpisodios'=> '', 'ErroMsg'=>'Nenhum episodio encontrado','Sql'=>$str_conteudos); //,'Sql'=>$str_conteudos);                
            }
            
            return $arr_result;
        }
    }


    function ContentConteudoEpisodioSessoes($id_episodio) {

        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'ConteudoEpisodioSessoes'=> '', 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {

            $arr_sessoes = array();

            $str_sessoes = " SELECT
            lo_conteudo_episodio_secoes.lo_id_episodio_secao AS SecaoID, 
            lo_conteudo_episodio_secoes.lo_episodio_secao_ordem AS SecaoOrdem, 
            (CASE
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_texto IS NOT NULL THEN 'texto' 
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_video IS NOT NULL THEN 'video' 
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_imagem IS NOT NULL THEN 'imagem'
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_download IS NOT NULL THEN 'download'
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_link IS NOT NULL THEN 'link'
            END) AS SecaoTipo,

            (CASE
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_texto IS NOT NULL THEN lo_conteudo_episodio_secoes.lo_episodio_secao_texto 
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_video IS NOT NULL THEN lo_conteudo_episodio_secoes.lo_episodio_secao_video 
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_imagem IS NOT NULL THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/',lo_conteudo_episodio_secoes.lo_episodio_secao_imagem)
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_download IS NOT NULL THEN lo_conteudo_episodio_secoes.lo_episodio_secao_download
                WHEN lo_conteudo_episodio_secoes.lo_episodio_secao_link IS NOT NULL THEN lo_conteudo_episodio_secoes.lo_episodio_secao_link
            END) AS SecaoValor,

            lo_conteudo_episodio_secoes.lo_episodio_secao_titulo AS SecaoTitulo         

            FROM
            lo_conteudo_episodio_secoes
            WHERE
            lo_conteudo_episodio_secoes.lo_id_conteudo_episodio = " . $id_episodio . "
            ORDER BY
            lo_conteudo_episodio_secoes.lo_episodio_secao_ordem";

            $rs_sessoes = mysqli_query($conn, $str_sessoes);	   
            $num_sessoes = mysqli_num_rows($rs_sessoes);    

            if ($num_sessoes > 0){
                while($rv = mysqli_fetch_assoc($rs_sessoes)) {
                    $arr_sessoes[] = $rv;
                }

                $arr_result = array('Registros'=> $num_sessoes,
                                    'Sessoes'=>$arr_sessoes,'ErroMsg'=>false); //, 'Sql'=>$str_conteudos);

            } else {
                $arr_result = array('Registros' => 0, 'Sessoes'=> '', 'ErroMsg'=>'Nenhuma sessao encontrada'); //,'Sql'=>$str_conteudos); //,'Sql'=>$str_conteudos);                
            }
            
            return $arr_result;
        }
    }    


    function ContentConteudosAluno($id_aluno) {

        $conn = bd_connect_livel();

        if ($conn) {
      
            $str_conteudos_aluno = "SELECT
                c001_alunos.c001_id_aluno_lo AS AlunoID,      
                lo_produtos.lo_id_conteudo AS ConteudoID,
                lo_vendas.lo_id_venda AS VendaID,
                lo_venda_itens.lo_id_venda_item AS VendaItemID,
                lo_produtos.lo_id_produto AS ProdutoID,
                lo_produtos_categorias.lo_id_produto_categoria AS ProdutoCategoriaID,
                lo_produtos_unidades_venda.lo_id_unidade AS ProdutoUnidadeID,
                lo_venda_itens.lo_item_vigencia_inicio AS ItemVigenciaInicio,
                lo_venda_itens.lo_item_vigencia_fim AS ItemVigenciaFim,
                lo_venda_itens.lo_item_quantidade AS ItemQuantidade,

                (SELECT
                COUNT(lo_acessos.lo_id_acesso)
                FROM lo_acessos
                WHERE lo_acessos.lo_id_venda_item = lo_venda_itens.lo_id_venda_item
                ) AS ItemNumAcessos

                FROM
                lo_venda_itens
                INNER JOIN lo_vendas ON lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda
                LEFT OUTER JOIN lo_produtos_valores ON lo_venda_itens.lo_id_produto_valor = lo_produtos_valores.lo_id_produto_valor
                LEFT OUTER JOIN lo_produtos ON lo_produtos_valores.lo_id_produto = lo_produtos.lo_id_produto
                LEFT OUTER JOIN lo_produtos_categorias ON lo_produtos.lo_id_produto_categoria = lo_produtos_categorias.lo_id_produto_categoria
                LEFT OUTER JOIN c001_alunos ON lo_vendas.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
                INNER JOIN lo_produtos_unidades_venda ON lo_produtos_valores.lo_id_unidade = lo_produtos_unidades_venda.lo_id_unidade
                WHERE lo_produtos_categorias.lo_id_produto_categoria = 3
                AND lo_vendas.c001_id_aluno_lo = " . $id_aluno . "
                HAVING (
                (ProdutoUnidadeID IN (1,2) 
                    AND DATE(NOW()) BETWEEN ItemVigenciaInicio AND ItemVigenciaFim)
                    AND ItemVigenciaInicio <= DATE(NOW())
                )
                OR
                (ProdutoUnidadeID = 3 AND (ItemNumAcessos < ItemQuantidade OR ItemNumAcessos IS NULL))
                OR ProdutoUnidadeID = 4;";    
        
            $rs_conteudos_aluno = mysqli_query($conn, $str_conteudos_aluno);	   
            $num_conteudos_aluno = mysqli_num_rows($rs_conteudos_aluno);    
            $arr_conteudos_aluno = array();

            if ($num_conteudos_aluno > 0){
                while($rv = mysqli_fetch_assoc($rs_conteudos_aluno)) {
                    $arr_conteudos_aluno[] = $rv['ConteudoID'];
                }  
            
            }
        
        }

        return implode( ',', $arr_conteudos_aluno);

    }

    function ContentConteudosTipo($conteudo_tipo, $conteudo_aluno) {

        $conn = bd_connect_livel();

        if ($conn) {
            $str_sql = "SELECT DISTINCT
            lo_conteudos.lo_id_conteudo AS ConteudoID
            FROM lo_conteudos
            INNER JOIN lo_produtos ON lo_conteudos.lo_id_conteudo = lo_produtos.lo_id_conteudo
            INNER JOIN lo_produtos_valores ON  lo_produtos.lo_id_produto = lo_produtos_valores.lo_id_produto
            WHERE lo_conteudos.lo_conteudo_data_exclusao IS NULL 
            AND lo_produtos_valores.lo_id_unidade " . ($conteudo_tipo == 'conteudo_free' ? '= 5' : '<> 5') . "
            AND lo_produtos_valores.lo_produto_valor_data_exclusao IS NULL
            AND lo_produtos.lo_id_produto_categoria = 3";

            if ($conteudo_aluno) {
                $str_sql .= " AND lo_conteudos.lo_id_conteudo NOT IN (" . $conteudo_aluno . ")";
            }

            $rs_content = mysqli_query($conn, $str_sql);	   

            while($r = mysqli_fetch_assoc($rs_content)) {
                $arr_conteudo_free[] = $r['ConteudoID'];
            } 
        }

        return implode( ',', $arr_conteudo_free );
    }
  
?>