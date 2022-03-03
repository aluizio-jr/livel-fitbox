<?php
    function vendaGravarItens($vendaId, $vendaItens, $conn, $dataVenda = false) {
        try {
            $totalItens = 0;
            if (!$dataVenda) $dataVenda = date('Y-m-d');
            
            $itens_count = count($vendaItens);
            
            for ($i=0; $i<$itens_count; $i++) {
                $itemID = 0;

                $planoVigenciaId = $vendaItens[$i]['id_plano_vigencia'];
                $turmaLiveId = $vendaItens[$i]['id_live_turma'];
                $rPlano = array();
                $r = array();

                $str_sql = 'SELECT
                    lo_plano_vigencias.lo_id_plano,
                    lo_plano_vigencias.lo_id_unidade,
                    lo_plano_vigencias.lo_plano_vigencia_qtde,
                    lo_plano_vigencias.lo_plano_vigencia_valor,
                    lo_plano_vigencias.lo_plano_vigencia_parcelas
                    FROM
                    lo_plano_vigencias
                    WHERE
                    lo_plano_vigencias.lo_id_plano_vigencia = ' . $planoVigenciaId;

                $rsPlano = mysqli_query($conn, $str_sql);	   
                $numPlano = mysqli_num_rows($rsPlano);
                if (!$numPlano > 0) throw new Exception("Dados do plano nao encontrados.");
                
                while($r = mysqli_fetch_assoc($rsPlano)) {
                    $planoId = $r['lo_id_plano'];
                    $planoUnidade = $r['lo_id_unidade'];
                    $planoQtde = $r['lo_plano_vigencia_qtde'];
                    $planoValor = $r['lo_plano_vigencia_valor'];
                    $planoParcelas = $r['lo_plano_vigencia_parcelas'];
                }

                $r = array();

                $str_sql = "SELECT
                    lo_plano_produtos.lo_id_plano_produto,
                    lo_plano_produtos.lo_id_produto_valor,
                    lo_plano_produtos.lo_id_produto_categoria,
                    lo_plano_produtos.lo_plano_produto_percent,
                    lo_produtos_valores.lo_id_unidade
                    FROM
                    lo_plano_produtos
                    LEFT OUTER JOIN lo_produtos_valores ON lo_plano_produtos.lo_id_produto_valor = lo_produtos_valores.lo_id_produto_valor
                    WHERE
                    lo_plano_produtos.lo_id_plano = " . $planoId;

                $rsItens = mysqli_query($conn, $str_sql);	   
                $numItens = mysqli_num_rows($rsItens);
                if (!$numItens > 0) throw new Exception("Nao foi possivel encontrar os itens do plano.");

                while($r = mysqli_fetch_assoc($rsItens)) {

                    $itemPlanoId = $r['lo_id_plano_produto'];

                    $itemVigenciaInicial = false;
                    $itemVigenciaFinal = false;

                    $idProdutoValor = $r['lo_id_produto_valor'];
                    $idProdutoCategoria = $r['lo_id_produto_categoria'];
                    $itemPercent = $r['lo_plano_produto_percent'];
                    $itemQuantidade = $planoQtde;

                    $itemUnidade = $idProdutoValor ? $r['lo_id_unidade'] : $planoUnidade;

                    if ($itemUnidade == 1 || $itemUnidade == 2) {    //VIGÊNCIA DIAS OU MENSAL
                        $itemVigenciaInicial = $dataVenda;
                        $itemVigenciaFinal = vigenciaCalcula($itemUnidade, $planoQtde, $vigenciaInicial);
                    
                    } else { //if ($itemUnidade == 6) {
                        $itemQuantidade = 1;
                    }

                    $itemValor = 0;
                    $itemValorFinal = 0;
                    $descontoValor = 0;
                    $descontoPercentual = 0;
                    $descontoCupom = false;

                    if (count($vendaItens[$i]['descontos'])) {
                        if ($vendaItens[$i]['descontos'][0]['id_plano_produto'] == $itemPlanoId) {
                            $descontoValor = $vendaItens[$i]['descontos'][0]['id_plano_produto'];
                            $descontoPercentual = $vendaItens[$i]['descontos'][0]['desconto_percentual'];
                            $descontoCupom = $vendaItens[$i]['descontos'][0]['id_cupom'];
                        }
                    }

                    $itemValor = $planoValor > 0 ? (($planoValor * $itemPercent) / 100) : 0;

                    if ($itemValor > 0 && ($descontoValor || $descontoPercentual))
                        $itemDescontoValor = $descontoValor ?: (($itemValor * $descontoPercentual) / 100);

                    $itemValorFinal = $itemValor - $itemDescontoValor;

                    $vendaValor += $itemValor;
                    $vendaValorFinal += $itemValorFinal;

                    $arrFilters = ['lo_id_venda' => $vendaId];
                    $itemID++; // = nextID('lo_venda_itens', 'lo_id_venda_item', $arrFilters);

                    if (!$itemID) throw new Exception("Nao foi possivel gerar o ID do item.");

                    $arrCampos = [
                        "lo_id_venda" =>  $vendaId,
                        "lo_id_venda_item" => $itemID,
                        "lo_id_produto_valor" => $idProdutoValor ?: false,
                        "lo_id_produto_categoria" => $idProdutoCategoria ?: false,
                        "lo_id_plano_vigencia" => $planoVigenciaId,
                        "lo_id_live_turma" => $turmaLiveId ?: false,
                        "lo_item_vigencia_inicio" => $itemVigenciaInicial ?: false,
                        "lo_item_vigencia_fim" => $itemVigenciaFinal ?: false,
                        "lo_item_quantidade" => $itemQuantidade ?: false,
                        "lo_item_valor" => str_replace(',', '.', $itemValor),
                        "lo_id_cupom" => $descontoCupom ?: false,
                        "lo_item_valor_desconto" => str_replace(',', '.', $itemDescontoValor),
                        "lo_item_valor_final" => str_replace(',', '.', $itemValorFinal)
                    ];

                    $str_sql = queryInsert("lo_venda_itens", $arrCampos);

                    mysqli_query($conn, $str_sql);
                    $result = mysqli_affected_rows($conn);
    
                    if($result <= 0) {                
                        throw new Exception("Nao foi possivel gravar a venda (itens): " . mysqli_error($conn)); 
                    }
                    
                    $totalItens++;
                }

            }

            return ["vendaItens" => $totalItens, "error" => false];

        } catch(Exception $e) {
            return ["vendaItens" => false, "error" => $e->getMessage()];
            
        }
    }