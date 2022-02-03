<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integração ao Gateway de pagamento
 * Método: Pagamento transação completa
 * Autor: Bryan Marvila
 */

// código único que identificará o pedido em sua base
$dados_envio["numeroTransacao"] = 32135484;
// Seu código de estabelecimento junto ao Gateway
$dados_envio["codigoEstabelecimento"] = 658468468;
/*
 * Os campos nomeTitularCartaoCredito, numeroCartaoCredito, codigoSeguranca,
 * dataValidadeCartao e parcelas são setadas apenas quando a forma de pagamento for do tipo Cartão de Crédito
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

//Código da forma de pagamento, a lista destes código é encontrada no Manual de Integração
$dados_envio["codigoFormaPagamento"] = 17;
//valor com a formatacao de 100 para transacoes com R$ 1,00, por exemplo
$dados_envio["valor"] = 1005; // R$ 10,05
$dados_envio["dadosUsuarioTransacao"]["bairroEnderecoComprador"] = "Valdibia";
$dados_envio["dadosUsuarioTransacao"]["bairroEnderecoEntrega"] = "Valdibia";
$dados_envio["dadosUsuarioTransacao"]["cepEnderecoComprador"] = "09820120";
$dados_envio["dadosUsuarioTransacao"]["cepEnderecoEntrega"] = "09820120";
$dados_envio["dadosUsuarioTransacao"]["cidadeEnderecoComprador"] = "São Bernardo do Campo";
$dados_envio["dadosUsuarioTransacao"]["cidadeEnderecoEntrega"] = "São Bernardo do Campo";
$dados_envio["dadosUsuarioTransacao"]["codigoCliente"] = "120";
//Caso não haja tipo de telefone, enviá-lo nulo sem aspas conforme mostrado no exemplo codigoTipoTelefoneAdicionalComprador
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
// o campo documentoComprador é o documento principal para a identificação do comprador como por exemplo cpf/cnpj
$dados_envio["dadosUsuarioTransacao"]["documentoComprador"] = "";
// documento secundário
$dados_envio["dadosUsuarioTransacao"]["documento2Comprador"] = 97281296703;
$dados_envio["dadosUsuarioTransacao"]["emailComprador"] = "email@dominio.com.br";
$dados_envio["dadosUsuarioTransacao"]["enderecoComprador"] = "Antônio Francisco Lisboa";
$dados_envio["dadosUsuarioTransacao"]["enderecoEntrega"] = "Antônio Francisco Lisboa";
$dados_envio["dadosUsuarioTransacao"]["estadoEnderecoComprador"] = "São Paulo";
$dados_envio["dadosUsuarioTransacao"]["estadoEnderecoEntrega"] = "São Paulo";
$dados_envio["dadosUsuarioTransacao"]["nomeComprador"] = "Avelino Pópó de Freitas";
$dados_envio["dadosUsuarioTransacao"]["numeroEnderecoComprador"] = "65";
$dados_envio["dadosUsuarioTransacao"]["numeroEnderecoEntrega"] = "65";
// sexoComprador (m = masculino, f = feminino)
$dados_envio["dadosUsuarioTransacao"]["sexoComprador"] = "m";
$dados_envio["dadosUsuarioTransacao"]["telefoneAdicionalComprador"];
$dados_envio["dadosUsuarioTransacao"]["telefoneAdicionalEntrega"];
$dados_envio["dadosUsuarioTransacao"]["telefoneComprador"] = 55549874;
$dados_envio["dadosUsuarioTransacao"]["telefoneEntrega"] = 56654848;
//tipoCliente (1 = Física, 2 = Jurídica)
$dados_envio["dadosUsuarioTransacao"]["tipoCliente"] = 1;
$dados_envio["IP"] = "192.168.15.10";
// idioma (1 = Português, 2 = Inglês, 3 = Espanhol)
$dados_envio["idioma"] = "1";

//itensDoPedido é um array com os itens do carrinho, no exemplo a seguir foi utilizado a venda de um único produto
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
 * urlCampainha: este é o endereço chamado quando há a alteração de
 * status de um pedido enviando para ele o codigoEstabelecimento e numeroTransacao
 */
$dados_envio["urlCampainha"] = "dominio/caminho/para_para_o_metodo/campainha";
/*
 *  as duas urls a seguir são utilizadas para requisições após um pagamento pelo site da operadora em questão,
 * retornando para a url urlRedirecionamentoNaoPago em caso de pagamento não efetuado com sucesso e  para pagamentos aceitos
 */
$dados_envio["urlRedirecionamentoNaoPago"] = "dominio/caminho/para_para_o_metodo/compra_nao_ok";
$dados_envio["urlRedirecionamentoPago"] = "dominio/caminho/para_para_o_metodo/compra_ok";
// valorDesconto com a formatacao de 100 para transacoes com R$ 1,00, por exemplo
$dados_envio["valorDesconto"] = 200; //R$ 2,00
//vencimentoBoleto formato dd/mm/yyyy, caso seja nulo o boleto adotará a data setada na configuração do estabelecimento no Gateway
$dados_envio["vencimentoBoleto"];

/*
 * Criação do objeto responsável por transformar
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
		[mensagemVenda] => Transação autorizada
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