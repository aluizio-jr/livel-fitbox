<?php

    function PlanosList($id_plano = NULL, $id_vigencia = NULL, $show_hidden = false, $show_trial = false) {

        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_planos = array();

            $str_sql = "SELECT
                lo_planos.lo_id_plano AS PlanoID,
                lo_planos.lo_plano_nome AS PlanoNome,
                lo_planos.lo_plano_descricao AS PlanoDescricao,
                lo_planos.lo_plano_trial AS PlanoTrial
                FROM
                lo_planos";

            if ($id_plano) {
                $str_sql .= " WHERE lo_planos.lo_id_plano = $id_plano";

            } else if ($id_vigencia) {
                $str_sql .= " WHERE lo_planos.lo_id_plano IN 
                                (SELECT lo_id_plano
                                FROM lo_plano_vigencias 
                                WHERE lo_id_plano_vigencia = $id_vigencia)";

            } else {
                $str_sql .= " WHERE lo_planos.lo_plano_ativo = 1";

                if (!$show_hidden) {
                    $str_sql .= " AND lo_planos.lo_plano_ocultar_store = 0";
                }

                if (!$show_trial) {
                    $str_sql .= " AND lo_planos.lo_plano_trial = 0";
                }
            }

            $str_sql .= " AND lo_planos.lo_plano_data_exclusao IS NULL";

            $rs_planos = mysqli_query($conn, $str_sql);	   

            $i=0;

            while($r = mysqli_fetch_assoc($rs_planos)) {
                
                $id_plano_res = $r['PlanoID'];
                
                $arr_planos[] = $r;

                $str_sql = "SELECT
                        (CASE
                            WHEN lo_treinos_guiados.lo_treino_guiado_nome IS NOT NULL THEN lo_treinos_guiados.lo_treino_guiado_nome
                            WHEN lo_lives.lo_live_nome IS NOT NULL THEN lo_lives.lo_live_nome
                            WHEN lo_conteudos.lo_conteudo_nome IS NOT NULL THEN CONCAT('Conteúdo: ', lo_conteudos.lo_conteudo_nome)
                            END
                        ) AS ItemNome
                        FROM
                        lo_plano_produtos
                        INNER JOIN lo_produtos_valores ON lo_plano_produtos.lo_id_produto_valor = lo_produtos_valores.lo_id_produto_valor
                        INNER JOIN lo_produtos ON lo_produtos_valores.lo_id_produto = lo_produtos.lo_id_produto
                        LEFT OUTER JOIN lo_lives ON lo_produtos.lo_id_live = lo_lives.lo_id_live
                        LEFT OUTER JOIN lo_treinos_guiados ON lo_produtos.lo_id_treino_guiado = lo_treinos_guiados.lo_id_treino_guiado
                        LEFT OUTER JOIN lo_conteudos ON lo_produtos.lo_id_conteudo = lo_conteudos.lo_id_conteudo
                        WHERE lo_plano_produtos.lo_id_plano = " .  $id_plano_res;

                $rs_itens = mysqli_query($conn, $str_sql);	   

                while($ri = mysqli_fetch_assoc($rs_itens)) {
                    $arr_planos[$i]['Itens'][] = $ri['ItemNome'];
                    //$arr_planos['Itens'][] = $ri;
                }

                $str_sql = "SELECT
                    lo_plano_vigencias.lo_id_plano_vigencia PlanoVigenciaID,
                    CONCAT(lo_plano_vigencias.lo_plano_vigencia_parcelas, 'x ', REPLACE(lo_plano_vigencias.lo_plano_vigencia_valor,'.',',')) AS VigenciaValor,
                    CASE 
                    WHEN (lo_plano_vigencias.lo_id_unidade = 2)
                    THEN
                            (CASE 
                                            WHEN lo_plano_vigencias.lo_plano_vigencia_qtde = 1 THEN 'MENSAL' 
                                            WHEN lo_plano_vigencias.lo_plano_vigencia_qtde = 2 THEN 'BIMESTRAL'
                                            WHEN lo_plano_vigencias.lo_plano_vigencia_qtde = 3 THEN 'TRIMESTRAL'
                                            WHEN lo_plano_vigencias.lo_plano_vigencia_qtde = 6 THEN 'SEMESTRAL'
                                            WHEN lo_plano_vigencias.lo_plano_vigencia_qtde = 12 THEN 'ANUAL'
                                            ELSE CONCAT(lo_plano_vigencias.lo_plano_vigencia_qtde,' MESES')
                            END)
                    ELSE
                            CONCAT(lo_produtos_unidades_venda.lo_unidade_descricao, ': ', lo_plano_vigencias.lo_plano_vigencia_qtde)
                    END  
                    AS VigenciaDescricao

                    FROM
                    lo_plano_vigencias
                    INNER JOIN lo_produtos_unidades_venda ON lo_plano_vigencias.lo_id_unidade = lo_produtos_unidades_venda.lo_id_unidade";

                if ($id_vigencia) {
                    $str_sql .= " WHERE lo_plano_vigencias.lo_id_plano_vigencia = " . $id_vigencia;
                    

                } else if ($id_plano_res) {
                    $str_sql .= " WHERE lo_plano_vigencias.lo_id_plano = $id_plano_res";
                }

                $str_sql .= " ORDER BY lo_plano_vigencias.lo_id_unidade, lo_plano_vigencias.lo_plano_vigencia_qtde";

                $rs_valores = mysqli_query($conn, $str_sql);	   

                while($rv = mysqli_fetch_assoc($rs_valores)) {
                    $arr_planos[$i]['Valores'][] = $rv;
                    //$arr_planos['Itens'][] = $ri;
                }

                $i++;                
            }                                     
        }

        $arr_result = array('Registros'=>count($arr_planos),'Planos'=>$arr_planos, 'Erro'=>$err_msg);

        return $arr_result;
    }

?>