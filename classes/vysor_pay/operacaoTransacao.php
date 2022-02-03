<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integra��o ao Gateway de pagamento
 * M�todo: Consulta Transacao Especifica
 * Autor: Bryan Marvila
 */

$dados_envio['codigoEstabelecimento'] = $codigoEstabelecimento;
$dados_envio['numeroTransacao'] = $numeroTransacao;
// opera��o = (2 = Cancelar, 1 = Capturar)
$dados_envio['operacao'] = 1;

/*
 * Cria��o do objeto respons�vel por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */
	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->operacaoTransacao($dados_envio);
print_r($soap);
// Exemplo do retorno obtido
/*
$soap(
	[return] => Array(
		[autorizacao] => 123456
		[codigoEstabelecimento] => 1355835042461
		[codigoFormaPagamento] => 121
		[codigoTransacaoOperadora] => 0
		[dataAprovacaoOperadora] => 11/09/2013
		[mensagemVenda] => Transa��o autorizada
		[numeroComprovanteVenda] => 10069930690815971001
		[numeroTransacao] => 536
		[parcelas] => 1
		[statusTransacao] => 15
		[taxaEmbarque] => 0
		[urlPagamento] => http://homologacao2.Gateway.com.br/Gateway/?cod=16548515301725e4d73ce-1437-4efc-9f17-3584803fa79b
		[valor] => 4000
		[valorDesconto] => 0
	)
)
/*