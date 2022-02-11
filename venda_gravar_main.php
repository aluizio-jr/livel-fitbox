<?php
    function vendaGravarMain($vendaData) {
        try {
            $vendaID = nextID('lo_vendas', 'lo_id_venda');
            if (!$vendaID) throw new Exception("Nao foi possivel gerar o ID da venda.");
            
            $dataVenda = $vendaData['data_venda'] ?: date('Y-m-d');
            $horaVenda = $vendaData['hora_venda'] ?: date('H:i:s');
            $statusVenda = 1;

            $str_sql = " INSERT INTO lo_vendas (
                lo_id_venda,
                c001_id_aluno_lo,
                lo_id_venda_tipo,
                lo_venda_data,
                lo_venda_hora,
                lo_id_venda_status,
                lo_id_venda_renovacao
                ) VALUES ("
                . $vendaID . ","
                . $vendaData['id_cliente'] . ","
                . $vendaData['id_venda_tipo'] . ","
                . "'" . $dataVenda . "',"
                . "'" . $horaVenda . "',"
                . $statusVenda . ","
                . $vendaData['id_venda_renovacao'] ?: "NULL";
                $str_sql .= ")";
                
                echo $str_sql;

                mysqli_query($conn, $str_sql);
                $result = mysqli_affected_rows($conn);

                if($result <= 0) {                
                    throw new Exception("Nao foi possivel gravar a venda (main): " . $conn . " - Erro: " . mysqli_error($conn)); 
                }

                return ["idVenda" => $vendaID, "error" => false];

        } catch(Exception $e) {
            return ["idVenda" => false, "error" => $e->getMessage()];
            
        }
    }