<?php
    function vendaGravarItens($vendaId, $vendaItens, $conn, $dataVenda = false) {
        try {
            $totalItens = 0;
            if (!$dataVenda) $dataVenda = date('Y-m-d');
            
            $itens_count = count($vendaItens);
            
            for ($i=0; $i<$itens_count; $i++) {

                $vendaItemID++;

                $vigenciaIni = $vendaItens[$i]['item_vigencia_inicio'] ? date ('Y-m-d', strtotime($vendaItens[$i]['item_vigencia_inicio'])) : false;
                $vigenciaFim = $vendaItens[$i]['item_vigencia_fim'] ? date ('Y-m-d', strtotime($vendaItens[$i]['item_vigencia_fim'])) : false;

                $arrCampos = [
                    "lo_id_venda" =>  $vendaId,
                    "lo_id_venda_item" => $vendaItemID,
                    "lo_id_item" => $vendaItens[$i]['id_item'],
                    "lo_id_combo_vigencia" => $vendaItens[$i]['id_combo_vigencia'] ?: false,
                    "lo_id_live_turma" => $vendaItens[$i]['id_live_turma'] ?: false,
                    "lo_item_vigencia_inicio" => $vigenciaIni,
                    "lo_item_vigencia_fim" => $vigenciaFim,
                    "lo_item_quantidade" => $vendaItens[$i]['item_quantidade'] ?: false,
                    "lo_item_valor" => str_replace(',', '.', $vendaItens[$i]['item_valor']),
                    "lo_id_cupom" => $vendaItens[$i]['id_cupom'] ?: false,
                    "lo_item_valor_desconto" => str_replace(',', '.', $vendaItens[$i]['item_valor_desconto']),
                    "lo_item_valor_final" => str_replace(',', '.', $vendaItens[$i]['item_valor_final'])
                ];

                $str_sql = queryInsert("lo_venda_itens", $arrCampos);

                mysqli_query($conn, $str_sql);
                $result = mysqli_affected_rows($conn);

                if($result <= 0) {                
                    throw new Exception("Nao foi possivel gravar a venda (itens): " . mysqli_error($conn)); 
                }

            }

            return ["vendaItens" => true, "error" => false];

        } catch(Exception $e) {
            return ["vendaItens" => false, "error" => $e->getMessage()];
            
        }
    }