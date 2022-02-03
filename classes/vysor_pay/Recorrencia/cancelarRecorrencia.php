<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integração ao Gateway de pagamento
 * Método: consultaTransacaoRecorrenciaWS
 * Autor: Bryan Marvila
 */

// Seu código de estabelecimento junto ao Gateway
$dados_envio["estabelecimento"] = 1373891021810;
// código único que identificará o pedido em sua base
$dados_envio['numeroRecorrencia'] = 6516816;

/*
 * Criação do objeto responsável por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->cancelarRecorrencia($dados_envio);
// Exemplo do retorno obtido
/*
 * 
$soap(
	[return] => Array(
		[estabelecimento] => 1355835042461
		[numeroRecorrencia] => 538
		[status] => false
		[valor] => 1005
	)
)
*/