<?php
    function AlunoContent($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_conteudos = array();
            $conteudo_cliente=false;

//ACESSOS TREINOS
            
            $treino_online_acesso = false;
            $acesso_treino_montado = false;
            $cliente_perfil = false;
            $num_itens = 0;

            for ($i=1; $i<3; $i++) {

                $str_sql = "SELECT
                c001_alunos.c001_id_aluno_lo AS AlunoID,
                c001_alunos.c001_id_cliente_box AS AlunoFitboxID,
                lo_vendas.c001_id_aluno_lo AS AlunoResponsavelID,
                lo_vendas.lo_id_venda AS VendaID,
                lo_venda_itens.lo_id_venda_item AS VendaItemID,
                lo_venda_itens.lo_id_live_turma AS TurmaLiveID,
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
                ) AS ItemNumAcessos,
                (SELECT
                COUNT(lo_transacoes.lo_id_transacao)
                FROM lo_transacoes
                WHERE lo_transacoes.lo_transacao_vencimento < DATE(NOW()) 
                AND (lo_transacoes.cs019b_id_spay_transacao_status NOT IN (1,31,99) OR lo_transacoes.cs019b_id_spay_transacao_status IS NULL)
                AND lo_transacoes.lo_id_venda = lo_vendas.lo_id_venda
                ) AS PendenciaFinan
                FROM
                lo_venda_itens
                INNER JOIN lo_vendas ON lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda
                LEFT OUTER JOIN lo_produtos_valores ON lo_venda_itens.lo_id_produto_valor = lo_produtos_valores.lo_id_produto_valor
                LEFT OUTER JOIN lo_produtos ON lo_produtos_valores.lo_id_produto = lo_produtos.lo_id_produto
                LEFT OUTER JOIN lo_produtos_categorias ON lo_produtos.lo_id_produto_categoria = lo_produtos_categorias.lo_id_produto_categoria
                LEFT OUTER JOIN c001_alunos ON lo_vendas.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
                INNER JOIN lo_produtos_unidades_venda ON lo_produtos_valores.lo_id_unidade = lo_produtos_unidades_venda.lo_id_unidade
                WHERE lo_produtos_categorias.lo_id_produto_categoria IN (" . ($i==1 ? '1,2':'3,4') . ")
                AND lo_vendas.lo_id_venda_status = 1
                HAVING (AlunoResponsavelID = " . $id_aluno . " OR AlunoID = " . $id_aluno . ")
                AND (
                (ProdutoUnidadeID IN (1,2) 
                    AND DATE(NOW()) BETWEEN ItemVigenciaInicio AND ItemVigenciaFim)
                    AND ItemVigenciaInicio <= DATE(NOW())
                )
                OR
                (ProdutoUnidadeID = 3 AND (ItemNumAcessos < ItemQuantidade OR ItemNumAcessos IS NULL))
                OR ProdutoUnidadeID = 4;";

                $rs_itens = mysqli_query($conn, $str_sql);	   
                $num_itens = mysqli_num_rows($rs_itens);    


                if ($num_itens > 0){
                    
                    while($r = mysqli_fetch_assoc($rs_itens)) {
                        //print_r($r);

                        if ($i==1) {
                            if ($r['ProdutoCategoriaID'] == 1) {
                                $treino_online_acesso = true;
                                $treino_online_turma_id = $r['TurmaLiveID'];
                                $cliente_perfil = 1;
                                $treino_online_pendencia_finan = ($r['PendenciaFinan']>0 ? true:false);

                            } else if ($r['ProdutoCategoriaID'] == 2) {
                                $acesso_treino_montado = true;
                                $treino_montado_pendencia_finan = ($r['PendenciaFinan']>0 ? true:false);

                                if (!$cliente_perfil) {
                                    $cliente_perfil = 2;
                                }
                            }

                        } else if ($i==2) {
                            $arr_conteudos[] = $r['ProdutoID'];
                            if (!$cliente_perfil) {
                                $cliente_perfil = ($r['AlunoFitboxID'] ? 3 : 4);
                            }
                        }
                    }                         
                }

            }

 //PRÓXIMA LIVE

            $arr_live_next = array();
            $treino_online_next_today = false;        

            for ($i = 1; $i <= 3; $i++) {
                $str_sql = "SELECT
                lo_lives_horarios.lo_id_live_horario AS LiveHorarioID,
                lo_lives_horarios.lo_id_live_turma AS LiveTurmaID,
                
                (CASE WHEN lo_lives.lo_live_imagem IS NOT NULL 
                    THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_lives.lo_live_imagem) 
                    ELSE NULL 
                END) AS LiveImagem,

                (lo_lives_horarios.lo_live_dia_semana - 1) AS LiveDiaSemana,";

                $str_sql .= "
                    (CASE lo_live_dia_semana
                        WHEN 1 THEN 'Dom'
                        WHEN 2 THEN 'Seg'
                        WHEN 3 THEN 'Ter'
                        WHEN 4 THEN 'Qua'
                        WHEN 5 THEN 'Qui'
                        WHEN 6 THEN 'Sex'
                        WHEN 7 THEN 'Sab'
                        END
                    ) AS LiveDiaSemanaNome,";


                $str_sql .= "TIME_FORMAT(lo_lives_horarios.lo_live_horario,'%H:%i') AS LiveHoriario,
                        @horario := CONCAT(DATE(NOW()),' ',lo_live_horario) AS horario,
                        TIMESTAMPDIFF(MINUTE,@horario,NOW()) AS horario_dif
                        FROM
                        lo_lives_horarios
                        INNER JOIN lo_lives_turmas ON lo_lives_turmas.lo_id_live_turma = lo_lives_horarios.lo_id_live_turma
                        INNER JOIN lo_lives ON lo_lives.lo_id_live = lo_lives_turmas.lo_id_live";

                // if ($treino_online_turma_id) {
                //     $str_sql .= " WHERE lo_lives_horarios.lo_id_live_turma =  " . $treino_online_turma_id;
                // }

                if ($i == 1) {
                    $str_sql .= " WHERE lo_lives_horarios.lo_live_dia_semana = DAYOFWEEK(DATE(NOW())) 
                                AND lo_lives_horarios.lo_live_horario >= TIME(NOW())";

                } else if ($i == 2) {
                    $str_sql .= " WHERE lo_lives_horarios.lo_live_dia_semana > DAYOFWEEK(DATE(NOW()))";

                }
                            
                $str_sql .= " ORDER BY lo_lives_horarios.lo_live_dia_semana, 
                            lo_lives_horarios.lo_live_horario
                            LIMIT 1";
    //echo $str_sql;

                $rs_live = mysqli_query($conn, $str_sql);	   
                $num_live = mysqli_num_rows($rs_live);    
                
                if ($num_live > 0){
                    while($r = mysqli_fetch_assoc($rs_live)) {
                        $arr_live_next = $r;
                    }
                }  
                
                if (count($arr_live_next) > 0) {
                    //echo $i . " - " . $str_sql;
                    break;
                }
            }
//TREINOS MONTADOS
            $arr_treino_semana = array();
            $arr_treinos_destaque = array();
            $arr_treinos_categorias = array();

            $arr_treino_semana = ContentTreinoSemana();
            $arr_treinos_destaque = ContentTreinosDestaque();
            $arr_treinos_categorias = ContentTreinosCategorias(True); 


//CONTENT HOME

            if (count($arr_conteudos) > 0) {
                $conteudo_cliente = implode(',', $arr_conteudos);

            }

            $arr_conteudo_cliente = array();    //TIPO 1
            $arr_conteudo_pago = array();       //TIPO 2
            $arr_conteudo_free = array();       //TIPO 3
            

            //conteudo_aluno: conteúdo comprado pelo aluno
            //conteudo_pago: conteúdo pago (ainda não comprado pelo aluno)
            //conteudo_free

            //CONTEÚDO ALUNO
            $arr_conteudo_cliente = ContentConteudosList($id_aluno, 'conteudo_aluno');
  
            //CONTEUDO PAGO 
            $arr_conteudo_pago = ContentConteudosList($id_aluno, 'conteudo_pago');

            //CONTEUDO FREE
            $arr_conteudo_free = ContentConteudosList($id_aluno, 'conteudo_free');
          
//CONTEÚDO DESTAQUE
            $arr_destaque = array();

            $str_sql = "SELECT
                lo_home_destaque.lo_id_home_destaque AS DestaqueID,
                lo_home_destaque.lo_destaque_titulo AS DestaqueDescricao,
                lo_home_destaque.lo_destaque_imagem AS DestaqueImagem,
                lo_home_destaque.lo_destaque_video AS DestaqueVideo,
                lo_home_destaque.lo_destaque_texto AS DestaqueTexto,
                lo_home_destaque.lo_id_plano AS DestaqueLinkPlano,
                lo_home_destaque.lo_id_produto AS DestaqueLinkProduto,
                lo_home_destaque.lo_destaque_url AS DestaqueLinkExterno
                FROM
                lo_home_destaque";

                if ($cliente_perfil) {
                    $str_sql .= " WHERE lo_home_destaque.lo_id_destaque_perfil = " . $cliente_perfil;
                }

                $str_sql .= " ORDER BY lo_home_destaque.lo_id_home_destaque DESC LIMIT 1";

                $rs_destaque = mysqli_query($conn, $str_sql);	   

                while($r = mysqli_fetch_assoc($rs_destaque)) {
                    $arr_destaque[] = $r;
                }          
        }

        $arr_result = array('TREINO_ONLINE'=> array('TreinoOnlineAcesso' => $treino_online_acesso, 
                                                    'TreinoOnlinePendenciaFinan' => $treino_online_pendencia_finan,
                                                    'TreinoOnlineNext' => $arr_live_next),
                            'TREINOS_MONTADOS'=>array('TreinosMontadosAcesso' => $acesso_treino_montado,
                                                    'TreinosMontadosPendenciaFinan' => $treino_montado_pendencia_finan,
                                                    'TreinosMontadosSemana' => $arr_treino_semana,
                                                    'TreinosMontadosDestacados' => $arr_treinos_destaque,
                                                    'TreinosMontadosCategorias' => $arr_treinos_categorias), 
                            'CONTEUDOS'=> array('Conteudo_Aluno' => $arr_conteudo_cliente,
                                                'Conteudo_Pago' => $arr_conteudo_pago,
                                                'Conteudo_Free' => $arr_conteudo_free),
                            'DESTAQUE'=>$arr_destaque
                        );

/*        $arr_result = array('TreinoOnline'=> $treino_online_acesso, 
                            'TreinoMontado'=> $acesso_treino_montado, 
                            'ConteudoCliente'=> $arr_conteudo_cliente,
                            'ConteudoPago'=> $arr_conteudo_pago,
                            'ConteudoFree'=> $arr_conteudo_free,
                            'DestaqueHome'=>$arr_destaque,
                            'BadgesHome'=>$arr_badges);
*/
        return $arr_result;
    }
?>