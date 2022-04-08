<?php
    function vendaGravarMain($vendaData, $idCliente, $conn) {
        try {
            $vendaID = nextID('lo_vendas', 'lo_id_venda', false, $conn);
            if (!$vendaID) throw new Exception("Nao foi possivel gerar o ID da venda.");
            
            $dataVenda = $vendaData['data_venda'] ?: date('Y-m-d');
            $dataVenda = date ('Y-m-d', strtotime($dataVenda));
            $horaVenda = $vendaData['hora_venda'] ?: date('H:i:s');
            $statusVenda = 1;
            $vendaRenovacaoId = $vendaData['id_venda_renovacao'] ?: "NULL";

            $str_sql = " INSERT INTO lo_vendas (
                lo_id_venda,
                c001_id_aluno_lo,
                lo_venda_data,
                lo_venda_hora,
                lo_id_venda_status,
                lo_id_venda_renovacao
                ) VALUES ("
                . $vendaID . ","
                . $idCliente . ","
                . "'" . $dataVenda . "',"
                . "'" . $horaVenda . "',"
                . $statusVenda . ","
                . $vendaRenovacaoId
                . ")";

                mysqli_query($conn, $str_sql);
                $result = mysqli_affected_rows($conn);

                if($result <= 0) {                
                    throw new Exception("Nao foi possivel gravar a venda (main): " . mysqli_error($conn) . $str_sql); 
                }

                return ["idVenda" => $vendaID, "error" => false];

        } catch(Exception $e) {
            return ["idVenda" => false, "error" => $e->getMessage()];
            
        }
    }