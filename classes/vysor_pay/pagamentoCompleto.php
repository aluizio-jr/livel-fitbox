<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integra��o ao Gateway de pagamento
 * M�todo: Pagamento transa��o completa
 * Autor: Bryan Marvila
 */

// c�digo �nico que identificar� o pedido em sua base
$dados_envio["numeroTransacao"] = 32135484;
// Seu c�digo de estabelecimento junto ao Gateway
$dados_envio["codigoEstabelecimento"] = 658468468;
/*
 * Os campos nomeTitularCartaoCredito, numeroCartaoCredito, codigoSeguranca,
 * dataValidadeCartao e parcelas s�o setadas apenas quando a forma de pagamento for do tipo Cart�o de Cr�dito
 */
if($formaPagamento == 'cartao') {
	$dados_envio["nomeTitularCartaoCredito"] = "Manoel Moreira";
	$dados_envio["numeroCartaoCredito"] = 5555666677778884;
	$dados_envio["codigoSeguranca"] = 321;
	$dados_envio["dataValidadeCartao"] = "12/2012"; //mm/yyyy
	$dados_envio["parcelas"] = 1;	
}

$dados_envio['campoLivre1'] = "Livre 16 caracteres";
$dados_envio['campoLivre2'] = "Livre 16 caracteres";
$dados_envio['campoLivre3'] = "Livre 16 caracteres";
$dados_envio['campoLivre4'] = "Livre 16 caracteres";
$dados_envio['campoLivre5'] = "Livre 16 caracteres";

//C�digo da forma de pagamento, a lista destes c�digo � encontrada no Manual de Integra��o
$dados_envio["codigoFormaPagamento"] = 17;
//valor com a formatacao de 100 para transacoes com R$ 1,00, por exemplo
$dados_envio["valor"] = 1005; // R$ 10,05
$dados_envio["dadosUsuarioTransacao"]["bairroEnderecoComprador"] = "Valdibia";
$dados_envio["dadosUsuarioTransacao"]["bairroEnderecoEntrega"] = "Valdibia";
$dados_envio["dadosUsuarioTransacao"]["cepEnderecoComprador"] = "09820120";
$dados_envio["dadosUsuarioTransacao"]["cepEnderecoEntrega"] = "09820120";
$dados_envio["dadosUsuarioTransacao"]["cidadeEnderecoComprador"] = "S�o Bernardo do Campo";
$dados_envio["dadosUsuarioTransacao"]["cidadeEnderecoEntrega"] = "S�o Bernardo do Campo";
$dados_envio["dadosUsuarioTransacao"]["codigoCliente"] = "120";
//Caso n�o haja tipo de telefone, envi�-lo nulo sem aspas conforme mostrado no exemplo codigoTipoTelefoneAdicionalComprador
$dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneAdicionalComprador"];
$dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneAdicionalEntrega"];
$dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneComprador"] = 1;
$dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneEntrega"] = 1;
$dados_envio["dadosUsuarioTransacao"]["complementoEnderecoComprador"] = "casa";
$dados_envio["dadosUsuarioTransacao"]["complementoEnderecoEntrega"] = "casa";
//Data de nascimento do comprador (formato dd/mm/yyyy)
$dados_envio["dadosUsuarioTransacao"]["dataNascimentoComprador"] = "10/10/1965";
$dados_envio["dadosUsuarioTransacao"]["dddAdicionalComprador"];
$dados_envio["dadosUsuarioTransacao"]["dddAdicionalEntrega"];
$dados_envio["dadosUsuarioTransacao"]["dddComprador"] = 12;
$dados_envio["dadosUsuarioTransacao"]["dddEntrega"] = 12;
$dados_envio["dadosUsuarioTransacao"]["ddiAdicionalComprador"];
$dados_envio["dadosUsuarioTransacao"]["ddiAdicionalEntrega"];
$dados_envio["dadosUsuarioTransacao"]["ddiComprador"] = 55;
$dados_envio["dadosUsuarioTransacao"]["ddiEntrega"] = 55;
// o campo documentoComprador � o documento principal para a identifica��o do comprador como por exemplo cpf/cnpj
$dados_envio["dadosUsuarioTransacao"]["documentoComprador"] = "";
// documento secund�rio
$dados_envio["dadosUsuarioTransacao"]["documento2Comprador"] = 97281296703;
$dados_envio["dadosUsuarioTransacao"]["emailComprador"] = "email@dominio.com.br";
$dados_envio["dadosUsuarioTransacao"]["enderecoComprador"] = "Ant�nio Francisco Lisboa";
$dados_envio["dadosUsuarioTransacao"]["enderecoEntrega"] = "Ant�nio Francisco Lisboa";
$dados_envio["dadosUsuarioTransacao"]["estadoEnderecoComprador"] = "S�o Paulo";
$dados_envio["dadosUsuarioTransacao"]["estadoEnderecoEntrega"] = "S�o Paulo";
$dados_envio["dadosUsuarioTransacao"]["nomeComprador"] = "Avelino P�p� de Freitas";
$dados_envio["dadosUsuarioTransacao"]["numeroEnderecoComprador"] = "65";
$dados_envio["dadosUsuarioTransacao"]["numeroEnderecoEntrega"] = "65";
// sexoComprador (m = masculino, f = feminino)
$dados_envio["dadosUsuarioTransacao"]["sexoComprador"] = "m";
$dados_envio["dadosUsuarioTransacao"]["telefoneAdicionalComprador"];
$dados_envio["dadosUsuarioTransacao"]["telefoneAdicionalEntrega"];
$dados_envio["dadosUsuarioTransacao"]["telefoneComprador"] = 55549874;
$dados_envio["dadosUsuarioTransacao"]["telefoneEntrega"] = 56654848;
//tipoCliente (1 = F�sica, 2 = Jur�dica)
$dados_envio["dadosUsuarioTransacao"]["tipoCliente"] = 1;
$dados_envio["IP"] = "192.168.15.10";
// idioma (1 = Portugu�s, 2 = Ingl�s, 3 = Espanhol)
$dados_envio["idioma"] = "1";

//itensDoPedido � um array com os itens do carrinho, no exemplo a seguir foi utilizado a venda de um �nico produto
$dados_envio["itensDoPedido"][0]["codigoProduto"] = "6548";
$dados_envio["itensDoPedido"][0]["codigoCategoria"] = "1";
$dados_envio["itensDoPedido"][0]["nomeProduto"] = "CG-150";
$dados_envio["itensDoPedido"][0]["quantidadeProduto"] = "1";
$dados_envio["itensDoPedido"][0]["valorUnitarioProduto"] = 50000;
$dados_envio["itensDoPedido"][0]["nomeCategoria"] = "Auto";
//fim itensDoPedido

//origemTransacao (1 = eCommerce, 2 = Mobile, 3 = URA, 4 = POS
$dados_envio["origemTransacao"] = "1";
//taxaEmbarque usado por companhia aerea
$dados_envio["taxaEmbarque"] = "0";
/*
 * urlCampainha: este � o endere�o chamado quando h� a altera��o de
 * status de um pedido enviando para ele o codigoEstabelecimento e numeroTransacao
 */
$dados_envio["urlCampainha"] = "dominio/caminho/para_para_o_metodo/campainha";
/*
 *  as duas urls a seguir s�o utilizadas para requisi��es ap�s um pagamento pelo site da operadora em quest�o,
 * retornando para a url urlRedirecionamentoNaoPago em caso de pagamento n�o efetuado com sucesso e  para pagamentos aceitos
 */
$dados_envio["urlRedirecionamentoNaoPago"] = "dominio/caminho/para_para_o_metodo/compra_nao_ok";
$dados_envio["urlRedirecionamentoPago"] = "dominio/caminho/para_para_o_metodo/compra_ok";
// valorDesconto com a formatacao de 100 para transacoes com R$ 1,00, por exemplo
$dados_envio["valorDesconto"] = 200; //R$ 2,00
//vencimentoBoleto formato dd/mm/yyyy, caso seja nulo o boleto adotar� a data setada na configura��o do estabelecimento no Gateway
$dados_envio["vencimentoBoleto"];

/*
 * Cria��o do objeto respons�vel por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */

include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->pagamentoCompleto($dados_envio);
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
*/