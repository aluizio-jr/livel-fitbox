<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integração ao Gateway de pagamento SuperPay
 * Método: Consulta Transacao Especifica
 * Autor: Bryan Marvila
 */

$dados_envio['codigoEstabelecimento'] = 654684684;
$dados_envio['numeroTransacao'] = 3546354;

/*
 * Criação do objeto responsável por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */
	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->consultaTransacaoEspecifica($dados_envio);
// Exemplo do retorno obtido
/*
$soap(
	[return] => Array(
		[autorizacao] => 0
		[codigoEstabelecimento] => 1355835042461
		[codigoFormaPagamento] => 17
		[codigoTransacaoOperadora] => 0
		[dataAprovacaoOperadora] => 
		[mensagemVenda] => 
		[numeroComprovanteVenda] => 
		[numeroTransacao] => 534
		[parcelas] => 1
		[statusTransacao] => 1
		[taxaEmbarque] => 0
		[urlPagamento] => 1378910135555e6079c52-dc0c-4703-b0af-3c16fad57aa1
		[valor] => 4000
		[valorDesconto] => 0
	)
)
*/